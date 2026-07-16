<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
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
            ->get(['id', 'name']);

        $summary = [
            'total_items' => Item::query()->count(),

            'total_stock' => Item::query()->sum('stock'),

            'total_value' => (float) Item::query()
                ->selectRaw(
                    'COALESCE(SUM(stock * purchase_price), 0) AS total_value'
                )
                ->value('total_value'),

            'low_stock_items' => Item::query()
                ->where('stock', '>', 0)
                ->whereColumn('stock', '<=', 'minimum_stock')
                ->count(),

            'out_of_stock_items' => Item::query()
                ->where('stock', 0)
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
    public function exportStock(Request $request): StreamedResponse
    {
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

                /*
                 * Menambahkan BOM agar karakter UTF-8 terbaca di Excel.
                 */
                fwrite($handle, "\xEF\xBB\xBF");

                /*
                 * Menulis judul kolom laporan.
                 */
                fputcsv(
                    $handle,
                    [
                        'No.',
                        'Kode Barang',
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
                    fputcsv(
                        $handle,
                        [
                            $number,
                            $item->code,
                            $item->name,
                            $item->category->name,
                            $item->unit,
                            $item->purchase_price,
                            $item->stock,
                            $item->minimum_stock,
                            $this->determineStockStatus($item),
                            $item->stock * $item->purchase_price,
                        ],
                        ';'
                    );

                    $number++;
                }

                fclose($handle);
            },
            $fileName,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
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
    private function getStockFilters(Request $request): array
    {
        $validated = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:150',
            ],

            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id'),
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
                (string) ($validated['search'] ?? '')
            ),

            'category_id' => isset($validated['category_id'])
                ? (int) $validated['category_id']
                : null,

            'stock_status' => $validated['stock_status'] ?? null,
        ];
    }

    /**
     * Membuat query laporan stok berdasarkan filter.
     */
    private function buildStockQuery(array $filters): Builder
    {
        return Item::query()
            ->when(
                $filters['search'] !== '',
                function (Builder $query) use ($filters): void {
                    $search = $filters['search'];

                    $query->where(
                        function (Builder $query) use ($search): void {
                            $query
                                ->where(
                                    'code',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
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
                                    function (Builder $categoryQuery) use (
                                        $search
                                    ): void {
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
                function (Builder $query) use ($filters): void {
                    $query->where(
                        'category_id',
                        $filters['category_id']
                    );
                }
            )
            ->when(
                $filters['stock_status'] === 'available',
                function (Builder $query): void {
                    $query->whereColumn(
                        'stock',
                        '>',
                        'minimum_stock'
                    );
                }
            )
            ->when(
                $filters['stock_status'] === 'low',
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
                $filters['stock_status'] === 'out',
                function (Builder $query): void {
                    $query->where('stock', 0);
                }
            );
    }

    /**
     * Menentukan status stok suatu barang.
     */
    private function determineStockStatus(Item $item): string
    {
        if ((int) $item->stock === 0) {
            return 'Habis';
        }

        if ((int) $item->stock <= (int) $item->minimum_stock) {
            return 'Menipis';
        }

        return 'Tersedia';
    }
}
