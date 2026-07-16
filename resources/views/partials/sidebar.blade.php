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
    </nav>
</aside>
