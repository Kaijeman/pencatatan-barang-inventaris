<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockOpname\StoreStockOpnameRequest;
use App\Models\Item;
use App\Models\StockOpname;
use App\Models\User;
use App\Notifications\StockAlertNotification;
use App\Notifications\StockOpnameDifferenceNotification;
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

        $differenceStatus = $request->input(
            'difference_status'
        );

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

                $systemStock = (int) $item->stock;

                $physicalStock = (int) $validated[
                    'physical_stock'
                ];

                $difference = $physicalStock - $systemStock;

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
                 * Menyesuaikan stok berdasarkan stok fisik.
                 */
                $item->stock = $physicalStock;
                $item->save();

                return $stockOpname;
            },
            3
        );

        /**
         * Memuat relasi untuk notifikasi dan tampilan.
         */
        $stockOpname->load([
            'item.category',
            'user:id,name',
        ]);

        $differenceNotificationQueued =
            $this->queueDifferenceNotification(
                $stockOpname
            );

        $stockAlerts = $this->buildStockAlerts(
            $stockOpname
        );

        $stockAlertQueued =
            $this->queueStockAlertNotification(
                $stockAlerts,
                $stockOpname
            );

        /**
         * Transaksi tetap berhasil walaupun notifikasi gagal.
         */
        if (
            ! $differenceNotificationQueued
            || ! $stockAlertQueued
        ) {
            return redirect()
                ->route(
                    'stock-opnames.show',
                    $stockOpname
                )
                ->with(
                    'error',
                    'Stock opname berhasil disimpan, tetapi terdapat notifikasi email yang gagal diproses.'
                );
        }

        $message =
            'Stock opname berhasil disimpan dan stok barang telah disesuaikan.';

        if ((int) $stockOpname->difference !== 0) {
            $message .=
                ' Notifikasi selisih stok telah dikirim.';
        }

        if ($stockAlerts !== []) {
            $message .=
                ' Peringatan stok menipis atau habis.';
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
     * Memasukkan notifikasi selisih stock opname.
     */
    private function queueDifferenceNotification(
        StockOpname $stockOpname
    ): bool {
        /**
         * Tidak mengirim email jika stok fisik sesuai.
         */
        if ((int) $stockOpname->difference === 0) {
            return true;
        }

        try {
            $recipients =
                $this->getDifferenceNotificationRecipients(
                    $stockOpname
                );

            if ($recipients->isEmpty()) {
                Log::warning(
                    'Notifikasi selisih stock opname tidak memiliki penerima.',
                    [
                        'stock_opname_id' =>
                            $stockOpname->id,
                    ]
                );

                return false;
            }

            $opnameData = [
                'item_code' =>
                    $stockOpname->item->code,

                'item_name' =>
                    $stockOpname->item->name,

                'category_name' =>
                    $stockOpname->item->category->name,

                'unit' =>
                    $stockOpname->item->unit,

                'system_stock' =>
                    (int) $stockOpname->system_stock,

                'physical_stock' =>
                    (int) $stockOpname->physical_stock,

                'difference' =>
                    (int) $stockOpname->difference,

                'opname_date' =>
                    $stockOpname
                        ->opname_date
                        ->format('d/m/Y'),

                'actor_name' =>
                    $stockOpname->user->name,

                'note' =>
                    $stockOpname->note,
            ];

            /**
             * Memasukkan notifikasi ke database queue.
             */
            Notification::send(
                $recipients,
                new StockOpnameDifferenceNotification(
                    $stockOpname->id,
                    $opnameData
                )
            );

            return true;
        } catch (Throwable $exception) {
            Log::error(
                'Notifikasi selisih stock opname gagal dimasukkan ke antrean.',
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
         * Tidak memperingatkan jika status tidak memburuk.
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

            /**
             * Memasukkan peringatan stok ke antrean.
             */
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
     * Mendapatkan kepala gudang dan petugas opname.
     */
    private function getDifferenceNotificationRecipients(
        StockOpname $stockOpname
    ) {
        return User::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where('email', 'not like', '%.test')
            ->where(
                function ($query) use (
                    $stockOpname
                ): void {
                    $query
                        ->where(
                            'role',
                            'kepala_gudang'
                        )
                        ->orWhere(
                            'id',
                            $stockOpname->user_id
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
