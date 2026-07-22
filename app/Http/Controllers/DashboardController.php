<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\GoodsIssue;
use App\Models\GoodsReceipt;
use App\Models\Item;
use App\Models\Supplier;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan ringkasan operasional gudang.
     */
    public function index(): View
    {
        $today = now()->toDateString();

        /**
         * Menghitung ringkasan data master.
         */
        $totalItems = Item::query()->count();

        $totalCategories = Category::query()->count();

        $totalSuppliers = Supplier::query()->count();

        $totalStock = (int) Item::query()->sum('stock');

        /**
         * Menghitung barang dengan stok menipis.
         */
        $lowStockItems = Item::query()
            ->where('stock', '>', 0)
            ->whereColumn(
                'stock',
                '<=',
                'minimum_stock'
            )
            ->count();

        /**
         * Menghitung barang yang stoknya habis.
         */
        $outOfStockItems = Item::query()
            ->where('stock', '<=', 0)
            ->count();

        /**
         * Menghitung transaksi pada hari ini.
         */
        $todayReceiptCount = GoodsReceipt::query()
            ->whereDate('received_at', $today)
            ->count();

        $todayIssueCount = GoodsIssue::query()
            ->whereDate('issued_at', $today)
            ->count();

        /**
         * Mengambil transaksi barang masuk terbaru.
         */
        $recentReceipts = GoodsReceipt::query()
            ->with([
                'supplier:id,name',
                'user:id,name',
            ])
            ->withSum('details', 'quantity')
            ->orderByDesc('received_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        /**
         * Mengambil transaksi barang keluar terbaru.
         */
        $recentIssues = GoodsIssue::query()
            ->with([
                'user:id,name',
            ])
            ->withSum('details', 'quantity')
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        /**
         * Mengambil barang yang memerlukan perhatian.
         */
        $stockAttentionItems = Item::query()
            ->with('category:id,name')
            ->whereColumn(
                'stock',
                '<=',
                'minimum_stock'
            )
            ->orderByRaw(
                'CASE WHEN stock <= 0 THEN 0 ELSE 1 END'
            )
            ->orderBy('stock')
            ->orderBy('name')
            ->limit(8)
            ->get();

        return view('dashboard.index', compact(
            'totalItems',
            'totalCategories',
            'totalSuppliers',
            'totalStock',
            'lowStockItems',
            'outOfStockItems',
            'todayReceiptCount',
            'todayIssueCount',
            'recentReceipts',
            'recentIssues',
            'stockAttentionItems'
        ));
    }
}
