<?php

namespace App\Http\Controllers;

use App\Http\Requests\Item\StoreItemRequest;
use App\Http\Requests\Item\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    /**
     * Menampilkan daftar barang.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $categoryId = $request->input('category_id');
        $stockStatus = $request->input('stock_status');

        $items = Item::query()
            ->with('category:id,name')
            ->when(
                $search !== '',
                function ($query) use ($search): void {
                    $query->where(
                        function ($query) use ($search): void {
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
                                    function ($categoryQuery) use (
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
                $categoryId,
                function ($query) use ($categoryId): void {
                    $query->where('category_id', $categoryId);
                }
            )
            ->when(
                $stockStatus === 'low',
                function ($query): void {
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
                $stockStatus === 'out',
                function ($query): void {
                    $query->where('stock', '<=', 0);
                }
            )
            ->when(
                $stockStatus === 'available',
                function ($query): void {
                    $query->whereColumn(
                        'stock',
                        '>',
                        'minimum_stock'
                    );
                }
            )
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return view('items.index', compact(
            'items',
            'categories',
            'search',
            'categoryId',
            'stockStatus'
        ));
    }

    /**
     * Menampilkan form tambah barang.
     */
    public function create(): View
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return view('items.create', compact('categories'));
    }

    /**
     * Menyimpan barang baru.
     */
    public function store(
        StoreItemRequest $request
    ): RedirectResponse {
        Item::create([
            ...$request->validated(),
            'stock' => 0,
        ]);

        return redirect()
            ->route('items.index')
            ->with(
                'success',
                'Barang berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan form edit barang.
     */
    public function edit(Item $item): View
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return view('items.edit', compact(
            'item',
            'categories'
        ));
    }

    /**
     * Memperbarui barang.
     */
    public function update(
        UpdateItemRequest $request,
        Item $item
    ): RedirectResponse {
        $item->update($request->validated());

        return redirect()
            ->route('items.index')
            ->with(
                'success',
                'Barang berhasil diperbarui.'
            );
    }

    /**
     * Menghapus barang.
     */
    public function destroy(Item $item): RedirectResponse
    {
        $hasTransactionHistory =
            $item->receiptDetails()->exists()
            || $item->issueDetails()->exists();

        if ($hasTransactionHistory) {
            return redirect()
                ->route('items.index')
                ->with(
                    'error',
                    'Barang tidak dapat dihapus karena memiliki riwayat transaksi.'
                );
        }

        if ((int) $item->stock > 0) {
            return redirect()
                ->route('items.index')
                ->with(
                    'error',
                    'Barang tidak dapat dihapus karena stoknya masih tersedia.'
                );
        }

        $item->delete();

        return redirect()
            ->route('items.index')
            ->with(
                'success',
                'Barang berhasil dihapus.'
            );
    }
}
