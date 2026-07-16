<x-layouts.app>
    <div class="grid grid-cols-4 gap-5">
        <div class="bg-white rounded-xl shadow p-5">
            <p>Total Barang</p>
            <h2 class="text-3xl font-bold">
                {{ $totalItems }}
            </h2>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <p>Total Supplier</p>
            <h2 class="text-3xl font-bold">
                {{ $totalSuppliers }}
            </h2>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <p>Total Kategori</p>
            <h2 class="text-3xl font-bold">
                {{ $totalCategories }}
            </h2>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <p>Stok Menipis</p>
            <h2 class="text-3xl font-bold text-red-600">
                {{ $lowStockItems }}
            </h2>
        </div>
    </div>
</x-layouts.app>
