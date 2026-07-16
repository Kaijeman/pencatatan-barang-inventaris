<aside
    id="sidebar"
    class="w-64 bg-slate-800 text-white transition-all duration-300">

    <div class="h-16 flex items-center justify-center border-b border-slate-700">
        <span class="text-xl font-bold">
            Inventory
        </span>
    </div>

    <nav class="mt-5">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-5 py-3 hover:bg-slate-700">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('categories.index') }}"
        class="flex items-center gap-3 px-5 py-3 transition
                {{ request()->routeIs('categories.*')
                    ? 'bg-slate-700 text-white'
                    : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">

            <i class="bi bi-tags"></i>

            <span>Kategori</span>
        </a>

        <a href="{{ route('suppliers.index') }}"
        class="flex items-center gap-3 px-5 py-3 transition
                {{ request()->routeIs('suppliers.*')
                    ? 'bg-slate-700 text-white'
                    : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">

            <i class="bi bi-truck"></i>

            <span>Supplier</span>
        </a>

        <a href="{{ route('items.index') }}"
        class="flex items-center gap-3 px-5 py-3 transition
                {{ request()->routeIs('items.*')
                    ? 'bg-slate-700 text-white'
                    : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">

            <i class="bi bi-box-seam"></i>

            <span>Barang</span>
        </a>

        <a href="{{ route('goods-receipts.index') }}"
        class="flex items-center gap-3 px-5 py-3 transition
                {{ request()->routeIs('goods-receipts.*')
                    ? 'bg-slate-700 text-white'
                    : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">

            <i class="bi bi-box-arrow-in-down"></i>

            <span>Barang Masuk</span>
        </a>

        <a href="{{ route('goods-issues.index') }}"
        class="flex items-center gap-3 px-5 py-3 transition
                {{ request()->routeIs('goods-issues.*')
                    ? 'bg-slate-700 text-white'
                    : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">

            <i class="bi bi-box-arrow-up"></i>

            <span>Barang Keluar</span>
        </a>

        <a href="{{ route('stock-opnames.index') }}"
        class="flex items-center gap-3 px-5 py-3 transition
                {{ request()->routeIs('stock-opnames.*')
                    ? 'bg-slate-700 text-white'
                    : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">

            <i class="bi bi-clipboard-check"></i>

            <span>Stock Opname</span>
        </a>

        <a href="{{ route('reports.stock') }}"
        class="flex items-center gap-3 px-5 py-3 transition
                {{ request()->routeIs('reports.stock*')
                    ? 'bg-slate-700 text-white'
                    : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">

            <i class="bi bi-clipboard-data"></i>

            <span>Laporan Stok</span>
        </a>
    </nav>
</aside>
