@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Dashboard
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Ringkasan data inventaris gudang.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Total Barang
                </p>

                <p class="mt-2 text-3xl font-bold text-slate-800">
                    {{ $totalItems }}
                </p>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Total Supplier
                </p>

                <p class="mt-2 text-3xl font-bold text-slate-800">
                    {{ $totalSuppliers }}
                </p>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Total Kategori
                </p>

                <p class="mt-2 text-3xl font-bold text-slate-800">
                    {{ $totalCategories }}
                </p>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Stok Menipis
                </p>

                <p class="mt-2 text-3xl font-bold text-red-600">
                    {{ $lowStockItems }}
                </p>
            </div>
        </div>
    </div>
@endsection
