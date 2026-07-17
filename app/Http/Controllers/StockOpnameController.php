<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockOpname\StoreStockOpnameRequest;
use App\Models\Item;
use App\Models\StockOpname;
use App\Models\User;
use App\Notifications\StockAlertNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class StockOpnameController extends Controller
{
    /**
     * Menampilkan daftar stock opname.
     */
    public function index(Request $request): View
    {
        $search = trim(
            (string) $request->input('search')
        );

        $date = $request->input('date');

        $differenceStatus =
            $request->input('difference_status');

        $stockOpnames = StockOpname::query()
            ->with([
                'item.category',
                'user:id,name',
            ])
            ->when(
                $search !== '',
                function ($query) use ($search): void {
                    $query->where(
                        function ($query) use ($search): void {
                            $query
                                ->whereHas(
                                    'item',
                                    function ($itemQuery) use (
                                        $search
                                    ): void {
                                        $itemQuery
                                            ->where(
                                                'code',
                                                'like',
                                                '%' . $search . '%'
                                            )
                                            ->orWhere(
                                                'name',
                                                'like',
                                                '%' . $search . '%'
                                            );
                                    }
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
                        'opname_date',
                        $date
                    );
                }
            )
            ->when(
                $differenceStatus === 'positive',
                function ($query): void {
                    $query->where(
                        'difference',
                        '>',
                        0
                    );
                }
            )
            ->when(
                $differenceStatus === 'negative',
                function ($query): void {
                    $query->where(
                        'difference',
                        '<',
                        0
                    );
                }
            )
            ->when(
                $differenceStatus === 'same',
                function ($query): void {
                    $query->where(
                        'difference',
                        0
                    );
                }
            )
            ->orderByDesc('opname_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('stock_opnames.index', compact(
            'stockOpnames',
            'search',
            'date',
            'differenceStatus'
        ));
    }

    /**
     * Menampilkan form stock opname.
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
                'minimum_stock',
            ]);

        return view('stock_opnames.create', compact(
            'items'
        ));
    }

    /**
     * Menyimpan stock opname dan menyesuaikan stok.
     */
    public function store(
        StoreStockOpnameRequest $request
    ): RedirectResponse {
        $validated = $request->validated();

        $stockOpname = DB::transaction(
            function () use ($validated): StockOpname {
                $item = Item::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $validated['item_id']
                    );

                $systemStock =
                    (int) $item->stock;

                $physicalStock =
                    (int) $validated['physical_stock'];

                $difference =
                    $physicalStock
                    - $systemStock;

                /**
                 * Mewajibkan catatan apabila terdapat selisih.
                 */
                if (
                    $difference !== 0
                    && blank($validated['note'] ?? null)
                ) {
                    throw ValidationException::withMessages([
                        'note' =>
                            'Catatan wajib diisi apabila terdapat selisih stok.',
                    ]);
                }

                $stockOpname = StockOpname::create([
                    'item_id' => $item->id,
                    'user_id' => auth()->id(),
                    'system_stock' => $systemStock,
                    'physical_stock' => $physicalStock,
                    'difference' => $difference,
                    'opname_date' =>
                        $validated['opname_date'],
                    'note' =>
                        $validated['note'] ?? null,
                ]);

                /**
                 * Menyesuaikan stok berdasarkan hasil fisik.
                 */
                $item->stock = $physicalStock;
                $item->save();

                return $stockOpname;
            },
            3
        );

        /**
         * Memuat relasi untuk tampilan dan notifikasi.
         */
        $stockOpname->load([
            'item.category',
            'user:id,name',
        ]);

        $stockAlerts =
            $this->buildStockAlerts($stockOpname);

        $stockAlertQueued =
            $this->queueStockAlertNotification(
                $stockAlerts,
                $stockOpname
            );

        if (
            $stockAlerts !== []
            && ! $stockAlertQueued
        ) {
            return redirect()
                ->route(
                    'stock-opnames.show',
                    $stockOpname
                )
                ->with(
                    'error',
                    'Stock opname berhasil disimpan, tetapi peringatan stok gagal dimasukkan ke antrean.'
                );
        }

        $message =
            'Stock opname berhasil disimpan dan stok barang telah disesuaikan.';

        if ($stockAlerts !== []) {
            $message .=
                ' Peringatan stok juga telah dimasukkan ke antrean email.';
        }

        return redirect()
            ->route(
                'stock-opnames.show',
                $stockOpname
            )
            ->with('success', $message);
    }

    /**
     * Menampilkan detail stock opname.
     */
    public function show(
        StockOpname $stockOpname
    ): View {
        $stockOpname->load([
            'item.category',
            'user',
        ]);

        return view('stock_opnames.show', compact(
            'stockOpname'
        ));
    }

    /**
     * Membuat data peringatan stok.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildStockAlerts(
        StockOpname $stockOpname
    ): array {
        $item = $stockOpname->item;

        $previousStock =
            (int) $stockOpname->system_stock;

        $currentStock =
            (int) $stockOpname->physical_stock;

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
         * Tidak membuat peringatan jika status tidak memburuk.
         */
        if (
            ! $this->shouldSendStockAlert(
                $previousStatus,
                $currentStatus
            )
        ) {
            return [];
        }

        return [
            [
                'item_id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'unit' => $item->unit,
                'previous_stock' => $previousStock,
                'current_stock' => $currentStock,
                'minimum_stock' =>
                    (int) $item->minimum_stock,
                'status' => $currentStatus,
            ],
        ];
    }

    /**
     * Memasukkan peringatan stok ke antrean.
     *
     * @param array<int, array<string, mixed>> $stockAlerts
     */
    private function queueStockAlertNotification(
        array $stockAlerts,
        StockOpname $stockOpname
    ): bool {
        if ($stockAlerts === []) {
            return true;
        }

        try {
            $recipients =
                $this->getHeadWarehouseRecipients();

            if ($recipients->isEmpty()) {
                Log::warning(
                    'Peringatan stok dari stock opname tidak memiliki penerima.',
                    [
                        'stock_opname_id' =>
                            $stockOpname->id,
                    ]
                );

                return false;
            }

            Notification::send(
                $recipients,
                new StockAlertNotification(
                    $stockAlerts,
                    'Stock Opname #'
                        . $stockOpname->id,
                    $stockOpname->user->name
                )
            );

            return true;
        } catch (Throwable $exception) {
            Log::error(
                'Peringatan stok dari stock opname gagal dimasukkan ke antrean.',
                [
                    'stock_opname_id' =>
                        $stockOpname->id,
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
     * Menentukan status stok.
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
