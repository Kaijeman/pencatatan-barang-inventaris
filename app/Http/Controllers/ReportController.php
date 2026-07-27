<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\GoodsIssue;
use App\Models\GoodsIssueDetail;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptDetail;
use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Menampilkan laporan stok barang.
     */
    public function stock(Request $request): View
    {
        $filters = $this->getStockFilters($request);

        $items = $this->buildStockQuery($filters)
            ->with('category:id,name')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        /**
         * Membuat ringkasan seluruh kondisi stok.
         */
        $summary = [
            'total_items' => Item::query()->count(),

            'total_stock' => Item::query()->sum('stock'),

            'total_value' => (float) Item::query()
                ->selectRaw(
                    'COALESCE(
                        SUM(stock * purchase_price),
                        0
                    ) AS total_value'
                )
                ->value('total_value'),

            'low_stock_items' => Item::query()
                ->where('stock', '>', 0)
                ->whereColumn(
                    'stock',
                    '<=',
                    'minimum_stock'
                )
                ->count(),

            'out_of_stock_items' => Item::query()
                ->where('stock', '<=', 0)
                ->count(),
        ];

        return view('reports.stock', compact(
            'items',
            'categories',
            'filters',
            'summary'
        ));
    }

    /**
     * Mengekspor laporan stok ke dalam file CSV.
     */
    public function exportStock(
        Request $request
    ): StreamedResponse {
        $filters = $this->getStockFilters($request);

        $fileName = 'laporan-stok-'
            . now()->format('Y-m-d-His')
            . '.csv';

        return Response::streamDownload(
            function () use ($filters): void {
                $handle = fopen('php://output', 'w');

                if ($handle === false) {
                    return;
                }

                /**
                 * Menambahkan BOM agar karakter UTF-8
                 * terbaca dengan benar di Excel.
                 */
                fwrite($handle, "\xEF\xBB\xBF");

                /**
                 * Menulis judul kolom laporan stok.
                 */
                fputcsv(
                    $handle,
                    [
                        'No.',
                        'Nama Barang',
                        'Kategori',
                        'Satuan',
                        'Harga Beli',
                        'Stok',
                        'Stok Minimum',
                        'Status',
                        'Nilai Persediaan',
                    ],
                    ';'
                );

                $number = 1;

                $items = $this->buildStockQuery($filters)
                    ->with('category:id,name')
                    ->orderBy('name')
                    ->cursor();

                foreach ($items as $item) {
                    $inventoryValue =
                        (float) $item->stock
                        * (float) $item->purchase_price;

                    fputcsv(
                        $handle,
                        [
                            $number,
                            $item->name,
                            $item->category?->name ?? '-',
                            $item->unit,
                            $item->purchase_price,
                            $item->stock,
                            $item->minimum_stock,
                            $this->determineStockStatus(
                                $item
                            ),
                            $inventoryValue,
                        ],
                        ';'
                    );

                    $number++;
                }

                fclose($handle);
            },
            $fileName,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',
            ]
        );
    }

    /**
     * Mengambil dan memvalidasi filter laporan stok.
     *
     * @return array{
     *     search: string,
     *     category_id: int|null,
     *     stock_status: string|null
     * }
     */
    private function getStockFilters(
        Request $request
    ): array {
        $validated = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:150',
            ],
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists(
                    'categories',
                    'id'
                ),
            ],
            'stock_status' => [
                'nullable',
                Rule::in([
                    'available',
                    'low',
                    'out',
                ]),
            ],
        ]);

        return [
            'search' => trim(
                (string) (
                    $validated['search']
                    ?? ''
                )
            ),

            'category_id' => isset(
                $validated['category_id']
            )
                ? (int) $validated['category_id']
                : null,

            'stock_status' =>
                $validated['stock_status']
                ?? null,
        ];
    }

    /**
     * Membuat query laporan stok berdasarkan filter.
     *
     * @param array{
     *     search: string,
     *     category_id: int|null,
     *     stock_status: string|null
     * } $filters
     */
    private function buildStockQuery(
        array $filters
    ): Builder {
        return Item::query()
            ->when(
                $filters['search'] !== '',
                function (
                    Builder $query
                ) use ($filters): void {
                    $search = $filters['search'];

                    $query->where(
                        function (
                            Builder $query
                        ) use ($search): void {
                            $query
                                ->where(
                                    'name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'unit',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhereHas(
                                    'category',
                                    function (
                                        Builder $categoryQuery
                                    ) use ($search): void {
                                        $categoryQuery->where(
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
                $filters['category_id'] !== null,
                function (
                    Builder $query
                ) use ($filters): void {
                    $query->where(
                        'category_id',
                        $filters['category_id']
                    );
                }
            )
            ->when(
                $filters['stock_status']
                    === 'available',
                function (Builder $query): void {
                    $query->whereColumn(
                        'stock',
                        '>',
                        'minimum_stock'
                    );
                }
            )
            ->when(
                $filters['stock_status']
                    === 'low',
                function (Builder $query): void {
                    $query
                        ->where('stock', '>', 0)
                        ->whereColumn(
                            'stock',
                            '<=',
                            'minimum_stock'
                        );
                }
            )
            ->when(
                $filters['stock_status']
                    === 'out',
                function (Builder $query): void {
                    $query->where(
                        'stock',
                        '<=',
                        0
                    );
                }
            );
    }

    /**
     * Menentukan status stok suatu barang.
     */
    private function determineStockStatus(
        Item $item
    ): string {
        if ((int) $item->stock <= 0) {
            return 'Habis';
        }

        if (
            (int) $item->stock
            <= (int) $item->minimum_stock
        ) {
            return 'Menipis';
        }

        return 'Tersedia';
    }

    /**
     * Menampilkan laporan transaksi barang masuk.
     */
    public function goodsReceipts(
        Request $request
    ): View {
        $filters = $this->getGoodsReceiptFilters(
            $request
        );

        $baseQuery = $this->buildGoodsReceiptQuery(
            $filters
        );

        /**
         * Membuat query ID transaksi agar
         * ringkasan mengikuti filter.
         */
        $filteredReceiptIds = function () use (
            $baseQuery
        ): Builder {
            return (clone $baseQuery)
                ->select('goods_receipts.id');
        };

        $summary = [
            'total_transactions' =>
                (clone $baseQuery)->count(),

            'total_quantity' => (int)
                GoodsReceiptDetail::query()
                    ->whereIn(
                        'goods_receipt_id',
                        $filteredReceiptIds()
                    )
                    ->sum('quantity'),

            'total_value' => (float)
                GoodsReceiptDetail::query()
                    ->whereIn(
                        'goods_receipt_id',
                        $filteredReceiptIds()
                    )
                    ->selectRaw(
                        'COALESCE(
                            SUM(
                                quantity
                                * purchase_price
                            ),
                            0
                        ) AS total_value'
                    )
                    ->value('total_value'),
        ];

        $receipts = (clone $baseQuery)
            ->with([
                'supplier:id,name',
                'user:id,name',
            ])
            ->withCount('details')
            ->withSum('details', 'quantity')
            ->addSelect([
                'total_value' =>
                    GoodsReceiptDetail::query()
                        ->selectRaw(
                            'COALESCE(
                                SUM(
                                    quantity
                                    * purchase_price
                                ),
                                0
                            )'
                        )
                        ->whereColumn(
                            'goods_receipt_id',
                            'goods_receipts.id'
                        ),
            ])
            ->orderByDesc('received_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $suppliers = Supplier::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return view(
            'reports.goods_receipts',
            compact(
                'receipts',
                'suppliers',
                'filters',
                'summary'
            )
        );
    }

    /**
     * Mengekspor laporan barang masuk ke file CSV.
     */
    public function exportGoodsReceipts(
        Request $request
    ): StreamedResponse {
        $filters = $this->getGoodsReceiptFilters(
            $request
        );

        $fileName = 'laporan-barang-masuk-'
            . now()->format('Y-m-d-His')
            . '.csv';

        return Response::streamDownload(
            function () use ($filters): void {
                $handle = fopen(
                    'php://output',
                    'w'
                );

                if ($handle === false) {
                    return;
                }

                /**
                 * Menambahkan BOM agar karakter UTF-8
                 * terbaca dengan benar di Excel.
                 */
                fwrite($handle, "\xEF\xBB\xBF");

                /**
                 * Menulis judul kolom laporan
                 * barang masuk.
                 */
                fputcsv(
                    $handle,
                    [
                        'No.',
                        'Tanggal',
                        'Supplier',
                        'Nama Barang',
                        'Kategori',
                        'Jumlah',
                        'Satuan',
                        'Harga Beli',
                        'Subtotal',
                        'Petugas',
                        'Catatan',
                    ],
                    ';'
                );

                $number = 1;

                $this->buildGoodsReceiptQuery(
                    $filters
                )
                    ->with([
                        'supplier:id,name',
                        'user:id,name',
                        'details.item:id,category_id,name,unit',
                        'details.item.category:id,name',
                    ])
                    ->chunkById(
                        200,
                        function (
                            $receipts
                        ) use (
                            $handle,
                            &$number
                        ): void {
                            foreach (
                                $receipts
                                as $receipt
                            ) {
                                foreach (
                                    $receipt->details
                                    as $detail
                                ) {
                                    $subtotal =
                                        (float)
                                        $detail->quantity
                                        * (float)
                                        $detail
                                            ->purchase_price;

                                    fputcsv(
                                        $handle,
                                        [
                                            $number,

                                            $receipt
                                                ->received_at
                                                ?->format(
                                                    'd/m/Y'
                                                ) ?? '-',

                                            $receipt
                                                ->supplier
                                                ?->name ?? '-',

                                            $detail
                                                ->item
                                                ?->name ?? '-',

                                            $detail
                                                ->item
                                                ?->category
                                                ?->name ?? '-',

                                            $detail->quantity,

                                            $detail
                                                ->item
                                                ?->unit ?? '-',

                                            $detail
                                                ->purchase_price,

                                            $subtotal,

                                            $receipt
                                                ->user
                                                ?->name ?? '-',

                                            $receipt->note,
                                        ],
                                        ';'
                                    );

                                    $number++;
                                }
                            }
                        }
                    );

                fclose($handle);
            },
            $fileName,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',
            ]
        );
    }

    /**
     * Mengambil dan memvalidasi filter
     * laporan barang masuk.
     *
     * @return array{
     *     search: string,
     *     supplier_id: int|null,
     *     start_date: string|null,
     *     end_date: string|null
     * }
     */
    private function getGoodsReceiptFilters(
        Request $request
    ): array {
        $validated = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:150',
            ],
            'supplier_id' => [
                'nullable',
                'integer',
                Rule::exists(
                    'suppliers',
                    'id'
                ),
            ],
            'start_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
                'before_or_equal:today',
            ],
        ]);

        return [
            'search' => trim(
                (string) (
                    $validated['search']
                    ?? ''
                )
            ),

            'supplier_id' => isset(
                $validated['supplier_id']
            )
                ? (int) $validated['supplier_id']
                : null,

            'start_date' =>
                $validated['start_date']
                ?? null,

            'end_date' =>
                $validated['end_date']
                ?? null,
        ];
    }

    /**
     * Membuat query laporan barang masuk
     * berdasarkan filter.
     *
     * @param array{
     *     search: string,
     *     supplier_id: int|null,
     *     start_date: string|null,
     *     end_date: string|null
     * } $filters
     */
    private function buildGoodsReceiptQuery(
        array $filters
    ): Builder {
        return GoodsReceipt::query()
            ->when(
                $filters['search'] !== '',
                function (
                    Builder $query
                ) use ($filters): void {
                    $search = $filters['search'];

                    $query->where(
                        function (
                            Builder $query
                        ) use ($search): void {
                            $query
                                ->where(
                                    'note',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhereHas(
                                    'supplier',
                                    function (
                                        Builder $supplierQuery
                                    ) use ($search): void {
                                        $supplierQuery
                                            ->where(
                                                'name',
                                                'like',
                                                '%' . $search . '%'
                                            );
                                    }
                                )
                                ->orWhereHas(
                                    'user',
                                    function (
                                        Builder $userQuery
                                    ) use ($search): void {
                                        $userQuery
                                            ->where(
                                                'name',
                                                'like',
                                                '%' . $search . '%'
                                            );
                                    }
                                )
                                ->orWhereHas(
                                    'details.item',
                                    function (
                                        Builder $itemQuery
                                    ) use ($search): void {
                                        $itemQuery
                                            ->where(
                                                'name',
                                                'like',
                                                '%' . $search . '%'
                                            )
                                            ->orWhere(
                                                'unit',
                                                'like',
                                                '%' . $search . '%'
                                            )
                                            ->orWhereHas(
                                                'category',
                                                function (
                                                    Builder $categoryQuery
                                                ) use ($search): void {
                                                    $categoryQuery
                                                        ->where(
                                                            'name',
                                                            'like',
                                                            '%'
                                                                . $search
                                                                . '%'
                                                        );
                                                }
                                            );
                                    }
                                );
                        }
                    );
                }
            )
            ->when(
                $filters['supplier_id'] !== null,
                function (
                    Builder $query
                ) use ($filters): void {
                    $query->where(
                        'supplier_id',
                        $filters['supplier_id']
                    );
                }
            )
            ->when(
                $filters['start_date'] !== null,
                function (
                    Builder $query
                ) use ($filters): void {
                    $query->whereDate(
                        'received_at',
                        '>=',
                        $filters['start_date']
                    );
                }
            )
            ->when(
                $filters['end_date'] !== null,
                function (
                    Builder $query
                ) use ($filters): void {
                    $query->whereDate(
                        'received_at',
                        '<=',
                        $filters['end_date']
                    );
                }
            );
    }

    /**
     * Menampilkan laporan transaksi barang keluar.
     */
    public function goodsIssues(
        Request $request
    ): View {
        $filters = $this->getGoodsIssueFilters(
            $request
        );

        $baseQuery = $this->buildGoodsIssueQuery(
            $filters
        );

        /**
         * Membuat query ID transaksi agar
         * ringkasan mengikuti filter.
         */
        $filteredIssueIds = function () use (
            $baseQuery
        ): Builder {
            return (clone $baseQuery)
                ->select('goods_issues.id');
        };

        $summary = [
            'total_transactions' =>
                (clone $baseQuery)->count(),

            'total_quantity' => (int)
                GoodsIssueDetail::query()
                    ->whereIn(
                        'goods_issue_id',
                        $filteredIssueIds()
                    )
                    ->sum('quantity'),

            'total_destinations' =>
                (clone $baseQuery)
                    ->distinct()
                    ->count('destination'),
        ];

        $issues = (clone $baseQuery)
            ->with([
                'user:id,name',
            ])
            ->withCount('details')
            ->withSum('details', 'quantity')
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view(
            'reports.goods_issues',
            compact(
                'issues',
                'filters',
                'summary'
            )
        );
    }

    /**
     * Mengekspor laporan barang keluar ke file CSV.
     */
    public function exportGoodsIssues(
        Request $request
    ): StreamedResponse {
        $filters = $this->getGoodsIssueFilters(
            $request
        );

        $fileName = 'laporan-barang-keluar-'
            . now()->format('Y-m-d-His')
            . '.csv';

        return Response::streamDownload(
            function () use ($filters): void {
                $handle = fopen(
                    'php://output',
                    'w'
                );

                if ($handle === false) {
                    return;
                }

                /**
                 * Menambahkan BOM agar karakter UTF-8
                 * terbaca dengan benar di Excel.
                 */
                fwrite($handle, "\xEF\xBB\xBF");

                /**
                 * Menulis judul kolom laporan
                 * barang keluar.
                 */
                fputcsv(
                    $handle,
                    [
                        'No.',
                        'Tanggal',
                        'Tujuan',
                        'Nama Barang',
                        'Kategori',
                        'Jumlah',
                        'Satuan',
                        'Petugas',
                        'Catatan',
                    ],
                    ';'
                );

                $number = 1;

                $this->buildGoodsIssueQuery(
                    $filters
                )
                    ->with([
                        'user:id,name',
                        'details.item:id,category_id,name,unit',
                        'details.item.category:id,name',
                    ])
                    ->chunkById(
                        200,
                        function (
                            $issues
                        ) use (
                            $handle,
                            &$number
                        ): void {
                            foreach (
                                $issues
                                as $issue
                            ) {
                                foreach (
                                    $issue->details
                                    as $detail
                                ) {
                                    fputcsv(
                                        $handle,
                                        [
                                            $number,

                                            $issue
                                                ->issued_at
                                                ?->format(
                                                    'd/m/Y'
                                                ) ?? '-',

                                            $issue->destination,

                                            $detail
                                                ->item
                                                ?->name ?? '-',

                                            $detail
                                                ->item
                                                ?->category
                                                ?->name ?? '-',

                                            $detail->quantity,

                                            $detail
                                                ->item
                                                ?->unit ?? '-',

                                            $issue
                                                ->user
                                                ?->name ?? '-',

                                            $issue->note,
                                        ],
                                        ';'
                                    );

                                    $number++;
                                }
                            }
                        }
                    );

                fclose($handle);
            },
            $fileName,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',
            ]
        );
    }

    /**
     * Mengambil dan memvalidasi filter
     * laporan barang keluar.
     *
     * @return array{
     *     search: string,
     *     start_date: string|null,
     *     end_date: string|null
     * }
     */
    private function getGoodsIssueFilters(
        Request $request
    ): array {
        $validated = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:150',
            ],
            'start_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
                'before_or_equal:today',
            ],
        ]);

        return [
            'search' => trim(
                (string) (
                    $validated['search']
                    ?? ''
                )
            ),

            'start_date' =>
                $validated['start_date']
                ?? null,

            'end_date' =>
                $validated['end_date']
                ?? null,
        ];
    }

    /**
     * Membuat query laporan barang keluar
     * berdasarkan filter.
     *
     * @param array{
     *     search: string,
     *     start_date: string|null,
     *     end_date: string|null
     * } $filters
     */
    private function buildGoodsIssueQuery(
        array $filters
    ): Builder {
        return GoodsIssue::query()
            ->when(
                $filters['search'] !== '',
                function (
                    Builder $query
                ) use ($filters): void {
                    $search = $filters['search'];

                    $query->where(
                        function (
                            Builder $query
                        ) use ($search): void {
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
                                    function (
                                        Builder $userQuery
                                    ) use ($search): void {
                                        $userQuery
                                            ->where(
                                                'name',
                                                'like',
                                                '%' . $search . '%'
                                            );
                                    }
                                )
                                ->orWhereHas(
                                    'details.item',
                                    function (
                                        Builder $itemQuery
                                    ) use ($search): void {
                                        $itemQuery
                                            ->where(
                                                'name',
                                                'like',
                                                '%' . $search . '%'
                                            )
                                            ->orWhere(
                                                'unit',
                                                'like',
                                                '%' . $search . '%'
                                            )
                                            ->orWhereHas(
                                                'category',
                                                function (
                                                    Builder $categoryQuery
                                                ) use ($search): void {
                                                    $categoryQuery
                                                        ->where(
                                                            'name',
                                                            'like',
                                                            '%'
                                                                . $search
                                                                . '%'
                                                        );
                                                }
                                            );
                                    }
                                );
                        }
                    );
                }
            )
            ->when(
                $filters['start_date'] !== null,
                function (
                    Builder $query
                ) use ($filters): void {
                    $query->whereDate(
                        'issued_at',
                        '>=',
                        $filters['start_date']
                    );
                }
            )
            ->when(
                $filters['end_date'] !== null,
                function (
                    Builder $query
                ) use ($filters): void {
                    $query->whereDate(
                        'issued_at',
                        '<=',
                        $filters['end_date']
                    );
                }
            );
    }
}
