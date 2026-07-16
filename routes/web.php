<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ItemController;
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
});

require __DIR__.'/auth.php';
