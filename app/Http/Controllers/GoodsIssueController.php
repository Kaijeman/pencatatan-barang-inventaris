<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoodsIssue\StoreGoodsIssueRequest;
use App\Models\GoodsIssue;
use App\Models\Item;
use App\Models\User;
use App\Notifications\GoodsIssueCreatedNotification;
use App\Notifications\StockAlertNotification;
use Illuminate\Database\Eloquent\Collection;
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
        $search = trim((string) $request->input('search'));
        $date = $request->input('date');

        $issues = GoodsIssue::query()
            ->with('user:id,name')
            ->withCount('details')
            ->withSum('details', 'quantity')
            ->when(
                $search !== '',
                function ($query) use ($search): void {
                    $query->where(
                        function ($query) use ($search): void {
                            $query
                                ->where(
                                    'destination',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'note',
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
                    $query->whereDate('issued_at', $date);
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
                'name',
                'unit',
                'stock',
            ]);

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
         * Menampung perubahan status stok.
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
                    'user_id' => auth()->id(),
                    'destination' => $validated['destination'],
                    'issued_at' => $validated['issued_at'],
                    'note' => $validated['note'] ?? null,
                ]);

                foreach (
                    $validated['items'] as $index => $detail
                ) {
                    $item = Item::query()
                        ->lockForUpdate()
                        ->findOrFail($detail['item_id']);

                    $requestedQuantity =
                        (int) $detail['quantity'];

                    $previousStock =
                        (int) $item->stock;

                    if ($requestedQuantity > $previousStock) {
                        throw ValidationException::withMessages([
                            "items.$index.quantity" =>
                                "Stok {$item->name} hanya tersedia "
                                . "{$previousStock} {$item->unit}.",
                        ]);
                    }

                    $currentStock =
                        $previousStock - $requestedQuantity;

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

                    $issue->details()->create([
                        'item_id' => $item->id,
                        'quantity' => $requestedQuantity,
                    ]);

                    $item->stock = $currentStock;
                    $item->save();

                    if (
                        $this->shouldSendStockAlert(
                            $previousStatus,
                            $currentStatus
                        )
                    ) {
                        $stockAlerts[] = [
                            'item_id' => $item->id,
                            'name' => $item->name,
                            'unit' => $item->unit,
                            'previous_stock' => $previousStock,
                            'current_stock' => $currentStock,
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

        $issue->load([
            'user:id,name',
            'details',
        ]);

        $transactionNotificationProcessed =
            $this->queueGoodsIssueNotification($issue);

        $stockAlertProcessed =
            $this->queueStockAlertNotification(
                $stockAlerts,
                $issue
            );

        if (
            ! $transactionNotificationProcessed
            || ! $stockAlertProcessed
        ) {
            return redirect()
                ->route('goods-issues.show', $issue)
                ->with(
                    'error',
                    'Transaksi barang keluar berhasil disimpan, tetapi terdapat notifikasi email yang gagal diproses.'
                );
        }

        return redirect()
            ->route('goods-issues.show', $issue)
            ->with(
                'success',
                'Transaksi barang keluar berhasil disimpan.'
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
     * Memasukkan notifikasi barang keluar ke antrean.
     */
    private function queueGoodsIssueNotification(
        GoodsIssue $issue
    ): bool {
        try {
            $recipients = $this->getNotificationRecipients();

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
                new GoodsIssueCreatedNotification($issue)
            );

            return true;
        } catch (Throwable $exception) {
            Log::error(
                'Notifikasi barang keluar gagal diproses.',
                [
                    'issue_id' => $issue->id,
                    'exception_class' => $exception::class,
                    'message' => $exception->getMessage(),
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
        if ($stockAlerts === []) {
            return true;
        }

        try {
            $recipients = $this->getNotificationRecipients();

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
                    'Barang keluar tanggal '
                        . $issue->issued_at->format('d/m/Y'),
                    $issue->user->name
                )
            );

            return true;
        } catch (Throwable $exception) {
            Log::error(
                'Peringatan stok gagal diproses.',
                [
                    'issue_id' => $issue->id,
                    'exception_class' => $exception::class,
                    'message' => $exception->getMessage(),
                ]
            );

            report($exception);

            return false;
        }
    }

    /**
     * Mendapatkan seluruh pengguna penerima notifikasi.
     */
    private function getNotificationRecipients(): Collection
    {
        return User::query()
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
     * Menentukan tingkat kondisi stok.
     */
    private function getStockSeverity(string $status): int
    {
        return match ($status) {
            'out' => 2,
            'low' => 1,
            default => 0,
        };
    }

    /**
     * Memeriksa apakah status stok memburuk.
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
