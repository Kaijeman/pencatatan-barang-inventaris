<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\GoodsReceiptController;
use App\Http\Controllers\GoodsIssueController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    /**
     * Route kategori.
     */
    Route::resource('categories', CategoryController::class)
        ->except(['show']);
    /**
     * Route supplier.
     */
    Route::resource('suppliers', SupplierController::class)
        ->except(['show']);
    /**
     * Route barang.
     */
    Route::resource('items', ItemController::class)
        ->except(['show']);
    /**
     * Route barang masuk.
     */
    Route::get('/goods-receipts', [GoodsReceiptController::class, 'index'])
        ->name('goods-receipts.index');
    Route::get('/goods-receipts/create', [GoodsReceiptController::class, 'create'])
        ->name('goods-receipts.create');
    Route::post('/goods-receipts', [GoodsReceiptController::class, 'store'])
        ->name('goods-receipts.store');
    Route::get('/goods-receipts/{goodsReceipt}', [GoodsReceiptController::class, 'show'])
        ->name('goods-receipts.show');
    /**
     * Route barang keluar.
     */
    Route::get('/goods-issues', [GoodsIssueController::class, 'index'])
        ->name('goods-issues.index');
    Route::get('/goods-issues/create', [GoodsIssueController::class, 'create'])
        ->name('goods-issues.create');
    Route::post('/goods-issues', [GoodsIssueController::class, 'store'])
        ->name('goods-issues.store');
    Route::get('/goods-issues/{goodsIssue}', [GoodsIssueController::class, 'show'])
        ->name('goods-issues.show');
    /**
     * Route stock opname.
     */
    Route::get('/stock-opnames', [StockOpnameController::class, 'index'])
        ->name('stock-opnames.index');
    Route::get('/stock-opnames/create', [StockOpnameController::class, 'create'])
        ->name('stock-opnames.create');
    Route::post('/stock-opnames', [StockOpnameController::class, 'store'])
        ->name('stock-opnames.store');
    Route::get('/stock-opnames/{stockOpname}', [StockOpnameController::class, 'show'])
        ->name('stock-opnames.show');
    /**
     * Route laporan stok.
     */
    Route::get('/reports/stock', [ReportController::class, 'stock'])
        ->name('reports.stock');
    Route::get('/reports/stock/export', [ReportController::class, 'exportStock'])
        ->name('reports.stock.export');
    /**
     * Route laporan barang masuk.
     */
    Route::get('/reports/goods-receipts/export', [ReportController::class, 'exportGoodsReceipts'])
        ->name('reports.goods-receipts.export');
    Route::get('/reports/goods-receipts', [ReportController::class, 'goodsReceipts'])
        ->name('reports.goods-receipts');
    /**
     * Route laporan barang keluar.
     */
    Route::get('/reports/goods-issues/export', [ReportController::class, 'exportGoodsIssues'])
        ->name('reports.goods-issues.export');
    Route::get('/reports/goods-issues', [ReportController::class, 'goodsIssues'])
        ->name('reports.goods-issues');
});

require __DIR__.'/auth.php';
