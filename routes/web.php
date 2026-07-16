<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\GoodsReceiptController;
use App\Http\Controllers\GoodsIssueController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::resource('categories', CategoryController::class)
        ->except(['show']);
    Route::resource('suppliers', SupplierController::class)
        ->except(['show']);
    Route::resource('items', ItemController::class)
        ->except(['show']);
    Route::get('/goods-receipts', [GoodsReceiptController::class, 'index'])
        ->name('goods-receipts.index');
    Route::get('/goods-receipts/create', [GoodsReceiptController::class, 'create'])
        ->name('goods-receipts.create');
    Route::post('/goods-receipts', [GoodsReceiptController::class, 'store'])
        ->name('goods-receipts.store');
    Route::get('/goods-receipts/{goodsReceipt}', [GoodsReceiptController::class, 'show'])
        ->name('goods-receipts.show');
    Route::get('/goods-issues', [GoodsIssueController::class, 'index'])
        ->name('goods-issues.index');
    Route::get('/goods-issues/create', [GoodsIssueController::class, 'create'])
        ->name('goods-issues.create');
    Route::post('/goods-issues', [GoodsIssueController::class, 'store'])
        ->name('goods-issues.store');
    Route::get('/goods-issues/{goodsIssue}', [GoodsIssueController::class, 'show'])
        ->name('goods-issues.show');
});

require __DIR__.'/auth.php';
