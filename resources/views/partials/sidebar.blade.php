{{-- Sidebar utama aplikasi. --}}
<aside
    id="sidebar"
    class="fixed inset-y-0 left-0 z-50 flex w-64
        -translate-x-full flex-col overflow-hidden
        bg-slate-800 text-white shadow-xl
        transition-transform duration-300 ease-in-out
        lg:translate-x-0"
>
    {{-- Identitas aplikasi. --}}
    <div
        class="flex h-16 flex-shrink-0 items-center
            justify-between border-b border-slate-700 px-5"
    >
        <span class="truncate text-xl font-bold">
            {{ config('app.name') }}
        </span>

        {{-- Tombol menutup sidebar pada perangkat kecil. --}}
        <button
            type="button"
            data-sidebar-toggle
            title="Tutup sidebar"
            aria-label="Tutup sidebar"
            class="inline-flex h-9 w-9 items-center
                justify-center rounded-lg text-slate-300
                transition hover:bg-slate-700 hover:text-white
                lg:hidden"
        >
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    {{-- Navigasi sidebar. --}}
    <nav class="flex-1 overflow-y-auto py-5">
        {{-- Menu dashboard. --}}
        <a
            href="{{ route('dashboard') }}"
            data-sidebar-link
            class="flex items-center gap-3 px-5 py-3 transition
                {{ request()->routeIs('dashboard')
                    ? 'bg-slate-700 text-white'
                    : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}"
        >
            <i
                class="bi bi-house flex w-5 flex-shrink-0
                    justify-center text-lg"
            ></i>

            <span class="whitespace-nowrap">
                Dashboard
            </span>
        </a>

        {{-- Menu kategori. --}}
        <a
            href="{{ route('categories.index') }}"
            data-sidebar-link
            class="flex items-center gap-3 px-5 py-3 transition
                {{ request()->routeIs('categories.*')
                    ? 'bg-slate-700 text-white'
                    : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}"
        >
            <i
                class="bi bi-tags flex w-5 flex-shrink-0
                    justify-center text-lg"
            ></i>

            <span class="whitespace-nowrap">
                Kategori
            </span>
        </a>

        {{-- Menu supplier. --}}
        <a
            href="{{ route('suppliers.index') }}"
            data-sidebar-link
            class="flex items-center gap-3 px-5 py-3 transition
                {{ request()->routeIs('suppliers.*')
                    ? 'bg-slate-700 text-white'
                    : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}"
        >
            <i
                class="bi bi-truck flex w-5 flex-shrink-0
                    justify-center text-lg"
            ></i>

            <span class="whitespace-nowrap">
                Supplier
            </span>
        </a>

        {{-- Menu barang. --}}
        <a
            href="{{ route('items.index') }}"
            data-sidebar-link
            class="flex items-center gap-3 px-5 py-3 transition
                {{ request()->routeIs('items.*')
                    ? 'bg-slate-700 text-white'
                    : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}"
        >
            <i
                class="bi bi-box-seam flex w-5 flex-shrink-0
                    justify-center text-lg"
            ></i>

            <span class="whitespace-nowrap">
                Barang
            </span>
        </a>

        {{-- Menu barang masuk. --}}
        <a
            href="{{ route('goods-receipts.index') }}"
            data-sidebar-link
            class="flex items-center gap-3 px-5 py-3 transition
                {{ request()->routeIs('goods-receipts.*')
                    ? 'bg-slate-700 text-white'
                    : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}"
        >
            <i
                class="bi bi-box-arrow-in-down flex w-5
                    flex-shrink-0 justify-center text-lg"
            ></i>

            <span class="whitespace-nowrap">
                Barang Masuk
            </span>
        </a>

        {{-- Menu barang keluar. --}}
        <a
            href="{{ route('goods-issues.index') }}"
            data-sidebar-link
            class="flex items-center gap-3 px-5 py-3 transition
                {{ request()->routeIs('goods-issues.*')
                    ? 'bg-slate-700 text-white'
                    : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}"
        >
            <i
                class="bi bi-box-arrow-up flex w-5
                    flex-shrink-0 justify-center text-lg"
            ></i>

            <span class="whitespace-nowrap">
                Barang Keluar
            </span>
        </a>

        {{-- Menu laporan stok. --}}
        <a
            href="{{ route('reports.stock') }}"
            data-sidebar-link
            class="flex items-center gap-3 px-5 py-3 transition
                {{ request()->routeIs('reports.stock*')
                    ? 'bg-slate-700 text-white'
                    : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}"
        >
            <i
                class="bi bi-clipboard-data flex w-5
                    flex-shrink-0 justify-center text-lg"
            ></i>

            <span class="whitespace-nowrap">
                Laporan Stok
            </span>
        </a>

        {{-- Menu laporan barang masuk. --}}
        <a
            href="{{ route('reports.goods-receipts') }}"
            data-sidebar-link
            class="flex items-center gap-3 px-5 py-3 transition
                {{ request()->routeIs('reports.goods-receipts*')
                    ? 'bg-slate-700 text-white'
                    : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}"
        >
            <i
                class="bi bi-file-earmark-arrow-down flex w-5
                    flex-shrink-0 justify-center text-lg"
            ></i>

            <span class="whitespace-nowrap">
                Laporan Barang Masuk
            </span>
        </a>

        {{-- Menu laporan barang keluar. --}}
        <a
            href="{{ route('reports.goods-issues') }}"
            data-sidebar-link
            class="flex items-center gap-3 px-5 py-3 transition
                {{ request()->routeIs('reports.goods-issues*')
                    ? 'bg-slate-700 text-white'
                    : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}"
        >
            <i
                class="bi bi-file-earmark-arrow-up flex w-5
                    flex-shrink-0 justify-center text-lg"
            ></i>

            <span class="whitespace-nowrap">
                Laporan Barang Keluar
            </span>
        </a>

        {{-- Menu manajemen pengguna. --}}
        <a
            href="{{ route('users.index') }}"
            data-sidebar-link
            class="flex items-center gap-3 px-5 py-3 transition
                {{ request()->routeIs('users.*')
                    ? 'bg-slate-700 text-white'
                    : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}"
        >
            <i
                class="bi bi-people flex w-5 flex-shrink-0
                    justify-center text-lg"
            ></i>

            <span class="whitespace-nowrap">
                Manajemen Pengguna
            </span>
        </a>
    </nav>
</aside>

{{-- Latar belakang ketika sidebar dibuka pada perangkat kecil. --}}
<div
    id="sidebar-overlay"
    class="fixed inset-0 z-40 hidden bg-slate-950/50
        backdrop-blur-[1px] lg:hidden"
></div>
