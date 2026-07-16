<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockOpname\StoreStockOpnameRequest;
use App\Models\Item;
use App\Models\StockOpname;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockOpnameController extends Controller
{
    /**
     * Menampilkan daftar riwayat stock opname.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $date = $request->input('date');
        $differenceStatus = $request->input('difference_status');

        $stockOpnames = StockOpname::query()
            ->with([
                'item.category',
                'user:id,name',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->whereHas('item', function ($itemQuery) use ($search) {
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
                        })
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where(
                                'name',
                                'like',
                                '%' . $search . '%'
                            );
                        });
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('opname_date', $date);
            })
            ->when(
                $differenceStatus === 'positive',
                function ($query) {
                    $query->where('difference', '>', 0);
                }
            )
            ->when(
                $differenceStatus === 'negative',
                function ($query) {
                    $query->where('difference', '<', 0);
                }
            )
            ->when(
                $differenceStatus === 'same',
                function ($query) {
                    $query->where('difference', 0);
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
     * Menampilkan form tambah stock opname.
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

        return view('stock_opnames.create', compact('items'));
    }

    /**
     * Menyimpan stock opname dan menyesuaikan stok barang.
     */
    public function store(
        StoreStockOpnameRequest $request
    ): RedirectResponse {
        $validated = $request->validated();

        $stockOpname = DB::transaction(function () use ($validated) {
            $item = Item::query()
                ->lockForUpdate()
                ->findOrFail($validated['item_id']);

            $systemStock = (int) $item->stock;
            $physicalStock = (int) $validated['physical_stock'];
            $difference = $physicalStock - $systemStock;
            $note = trim((string) ($validated['note'] ?? ''));

            /*
             * Mewajibkan alasan jika terdapat perbedaan stok.
             */
            if ($difference !== 0 && $note === '') {
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
                'opname_date' => $validated['opname_date'],
                'note' => $note !== '' ? $note : null,
            ]);

            /*
             * Menyesuaikan stok sistem dengan jumlah fisik.
             */
            $item->stock = $physicalStock;
            $item->save();

            return $stockOpname;
        }, 3);

        return redirect()
            ->route('stock-opnames.show', $stockOpname)
            ->with('success', 'Stock opname berhasil disimpan.');
    }

    /**
     * Menampilkan detail stock opname.
     */
    public function show(StockOpname $stockOpname): View
    {
        $stockOpname->load([
            'item.category',
            'user',
        ]);

        return view('stock_opnames.show', compact('stockOpname'));
    }
}
