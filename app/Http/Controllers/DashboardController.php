<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', [
            'totalItems' => Item::count(),
            'totalCategories' => Category::count(),
            'totalSuppliers' => Supplier::count(),
            'lowStockItems' => Item::whereColumn(
                'stock',
                '<=',
                'minimum_stock'
            )->count(),
        ]);
    }
}
