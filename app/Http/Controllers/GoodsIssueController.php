<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoodsIssue\StoreGoodsIssueRequest;
use App\Models\GoodsIssue;
use App\Models\Item;
use App\Models\User;
use App\Notifications\GoodsIssueCreatedNotification;
use App\Notifications\StockAlertNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class GoodsIssueController extends Controller
{
    /**
     * Menampilkan daftar transaksi barang keluar.
     */
    public function index(Request $request): View
    {
        $search = trim(
            (string) $request->input('search')
        );

        $date = $request->input('date');

        $issues = GoodsIssue::query()
            ->with([
                'user:id,name',
            ])
            ->withCount('details')
            ->withSum('details', 'quantity')
            ->when(
                $search !== '',
                function ($query) use ($search): void {
                    $query->where(
                        function ($query) use ($search): void {
                            $query
                                ->where(
                                    'issue_number',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'destination',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhereHas(
                                    'user',
                                    function ($userQuery) use (
                                        $search
                                    ): void {
                                        $userQuery->where(
                                            'name',
                                            'like',
                                            '%' . $search . '%'
                                        );
                                    }
                                );
                        }
                    );
                }
            )
            ->when(
                $date,
                function ($query) use ($date): void {
                    $query->whereDate(
                        'issued_at',
                        $date
                    );
                }
            )
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('goods_issues.index', compact(
            'issues',
            'search',
            'date'
        ));
    }

    /**
     * Menampilkan form transaksi barang keluar.
     */
    public function create(): View
    {
        $items = Item::query()
            ->with('category:id,name')
            ->orderBy('name')
            ->get([
                'id',
                'category_id',
                'code',
                'name',
                'unit',
                'stock',
            ]);

        /**
         * Memeriksa apakah masih ada barang yang tersedia.
         */
        $hasAvailableItems = $items->contains(
            fn (Item $item): bool =>
                (int) $item->stock > 0
        );

        return view('goods_issues.create', compact(
            'items',
            'hasAvailableItems'
        ));
    }

    /**
     * Menyimpan transaksi barang keluar.
     */
    public function store(
        StoreGoodsIssueRequest $request
    ): RedirectResponse {
        $validated = $request->validated();

        /**
         * Menampung barang yang status stoknya memburuk.
         *
         * @var array<int, array<string, mixed>> $stockAlerts
         */
        $stockAlerts = [];

        $issue = DB::transaction(
            function () use (
                $validated,
                &$stockAlerts
            ): GoodsIssue {
                $issue = GoodsIssue::create([
                    'issue_number' =>
                        $this->generateIssueNumber(),

                    'user_id' => auth()->id(),

                    'destination' =>
                        $validated['destination'],

                    'issued_at' =>
                        $validated['issued_at'],

                    'note' =>
                        $validated['note'] ?? null,
                ]);

                foreach (
                    $validated['items'] as $index => $detail
                ) {
                    $item = Item::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $detail['item_id']
                        );

                    $requestedQuantity =
                        (int) $detail['quantity'];

                    $previousStock =
                        (int) $item->stock;

                    /**
                     * Mencegah pengeluaran melebihi stok.
                     */
                    if (
                        $requestedQuantity
                        > $previousStock
                    ) {
                        throw ValidationException::withMessages([
                            "items.$index.quantity" =>
                                "Stok {$item->name} hanya tersedia "
                                . "{$previousStock} {$item->unit}.",
                        ]);
                    }

                    /**
                     * Menghitung stok setelah pengeluaran.
                     */
                    $currentStock =
                        $previousStock
                        - $requestedQuantity;

                    $previousStatus =
                        $this->determineStockStatus(
                            $previousStock,
                            (int) $item->minimum_stock
                        );

                    $currentStatus =
                        $this->determineStockStatus(
                            $currentStock,
                            (int) $item->minimum_stock
                        );

                    /**
                     * Menyimpan detail barang keluar.
                     */
                    $issue->details()->create([
                        'item_id' => $item->id,
                        'quantity' =>
                            $requestedQuantity,
                    ]);

                    /**
                     * Memperbarui stok barang.
                     */
                    $item->stock = $currentStock;
                    $item->save();

                    /**
                     * Menambahkan peringatan jika kondisi stok memburuk.
                     */
                    if (
                        $this->shouldSendStockAlert(
                            $previousStatus,
                            $currentStatus
                        )
                    ) {
                        $stockAlerts[] = [
                            'item_id' => $item->id,
                            'code' => $item->code,
                            'name' => $item->name,
                            'unit' => $item->unit,
                            'previous_stock' =>
                                $previousStock,
                            'current_stock' =>
                                $currentStock,
                            'minimum_stock' =>
                                (int) $item->minimum_stock,
                            'status' => $currentStatus,
                        ];
                    }
                }

                return $issue;
            },
            3
        );

        /**
         * Memuat relasi yang diperlukan notifikasi transaksi.
         */
        $issue->load([
            'user:id,name',
            'details',
        ]);

        $transactionNotificationQueued =
            $this->queueGoodsIssueNotification(
                $issue
            );

        $stockAlertQueued =
            $this->queueStockAlertNotification(
                $stockAlerts,
                $issue
            );

        if (
            $transactionNotificationQueued
            && $stockAlertQueued
        ) {
            $message =
                'Transaksi barang keluar berhasil disimpan ';

            if ($stockAlerts !== []) {
                $message .=
                    ' Peringatan stok menipis atau habis ';
            }

            return redirect()
                ->route(
                    'goods-issues.show',
                    $issue
                )
                ->with('success', $message);
        }

        return redirect()
            ->route(
                'goods-issues.show',
                $issue
            )
            ->with(
                'error',
                'Transaksi barang keluar berhasil disimpan, tetapi terdapat notifikasi yang gagal dimasukkan ke antrean.'
            );
    }

    /**
     * Menampilkan detail transaksi barang keluar.
     */
    public function show(
        GoodsIssue $goodsIssue
    ): View {
        $goodsIssue->load([
            'user',
            'details.item.category',
        ]);

        $totalQuantity = $goodsIssue
            ->details
            ->sum('quantity');

        return view('goods_issues.show', compact(
            'goodsIssue',
            'totalQuantity'
        ));
    }

    /**
     * Membuat nomor transaksi barang keluar.
     */
    private function generateIssueNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = 'BK-' . $date . '-';

        $lastNumber = GoodsIssue::query()
            ->where(
                'issue_number',
                'like',
                $prefix . '%'
            )
            ->lockForUpdate()
            ->orderByDesc('issue_number')
            ->value('issue_number');

        $nextSequence = $lastNumber
            ? ((int) substr($lastNumber, -4)) + 1
            : 1;

        return $prefix . str_pad(
            (string) $nextSequence,
            4,
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * Memasukkan notifikasi barang keluar ke antrean.
     */
    private function queueGoodsIssueNotification(
        GoodsIssue $issue
    ): bool {
        try {
            $recipients =
                $this->getTransactionNotificationRecipients();

            if ($recipients->isEmpty()) {
                Log::warning(
                    'Notifikasi barang keluar tidak memiliki penerima.',
                    [
                        'issue_id' => $issue->id,
                    ]
                );

                return false;
            }

            Notification::send(
                $recipients,
                new GoodsIssueCreatedNotification(
                    $issue
                )
            );

            return true;
        } catch (Throwable $exception) {
            Log::error(
                'Notifikasi barang keluar gagal dimasukkan ke antrean.',
                [
                    'issue_id' => $issue->id,
                    'exception_class' =>
                        $exception::class,
                    'message' =>
                        $exception->getMessage(),
                ]
            );

            report($exception);

            return false;
        }
    }

    /**
     * Memasukkan peringatan stok ke antrean.
     *
     * @param array<int, array<string, mixed>> $stockAlerts
     */
    private function queueStockAlertNotification(
        array $stockAlerts,
        GoodsIssue $issue
    ): bool {
        /**
         * Tidak perlu mengirim email jika stok masih aman.
         */
        if ($stockAlerts === []) {
            return true;
        }

        try {
            $recipients =
                $this->getHeadWarehouseRecipients();

            if ($recipients->isEmpty()) {
                Log::warning(
                    'Peringatan stok tidak memiliki penerima.',
                    [
                        'issue_id' => $issue->id,
                    ]
                );

                return false;
            }

            Notification::send(
                $recipients,
                new StockAlertNotification(
                    $stockAlerts,
                    'Barang Keluar '
                        . $issue->issue_number,
                    $issue->user->name
                )
            );

            return true;
        } catch (Throwable $exception) {
            Log::error(
                'Peringatan stok gagal dimasukkan ke antrean.',
                [
                    'issue_id' => $issue->id,
                    'exception_class' =>
                        $exception::class,
                    'message' =>
                        $exception->getMessage(),
                ]
            );

            report($exception);

            return false;
        }
    }

    /**
     * Mendapatkan kepala gudang dan pembuat transaksi.
     */
    private function getTransactionNotificationRecipients()
    {
        return User::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where('email', 'not like', '%.test')
            ->where(
                function ($query): void {
                    $query
                        ->where(
                            'role',
                            'kepala_gudang'
                        )
                        ->orWhere(
                            'id',
                            auth()->id()
                        );
                }
            )
            ->get()
            ->unique('email')
            ->values();
    }

    /**
     * Mendapatkan seluruh kepala gudang.
     */
    private function getHeadWarehouseRecipients()
    {
        return User::query()
            ->where('role', 'kepala_gudang')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where('email', 'not like', '%.test')
            ->get()
            ->unique('email')
            ->values();
    }

    /**
     * Menentukan status stok barang.
     */
    private function determineStockStatus(
        int $stock,
        int $minimumStock
    ): string {
        if ($stock <= 0) {
            return 'out';
        }

        if ($stock <= $minimumStock) {
            return 'low';
        }

        return 'available';
    }

    /**
     * Menentukan tingkat keparahan status stok.
     */
    private function getStockSeverity(
        string $status
    ): int {
        return match ($status) {
            'out' => 2,
            'low' => 1,
            default => 0,
        };
    }

    /**
     * Memeriksa apakah kondisi stok memburuk.
     */
    private function shouldSendStockAlert(
        string $previousStatus,
        string $currentStatus
    ): bool {
        return in_array(
            $currentStatus,
            [
                'low',
                'out',
            ],
            true
        )
            && $this->getStockSeverity($currentStatus)
                > $this->getStockSeverity($previousStatus);
    }
}
