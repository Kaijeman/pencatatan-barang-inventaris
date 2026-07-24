@php
    /**
     * Mendapatkan pengguna yang sedang login.
     */
    $authenticatedUser = auth()->user();

    /**
     * Membuat inisial nama pengguna.
     */
    $userInitial = strtoupper(
        mb_substr($authenticatedUser->name, 0, 1)
    );
@endphp

<nav
    class="sticky top-0 z-30 border-b border-slate-200
        bg-white shadow-sm"
>
    <div
        class="flex min-h-16 items-center justify-between
            gap-4 px-4 py-3 sm:px-6"
    >
        {{-- Bagian kiri navbar. --}}
        <div class="flex min-w-0 items-center gap-3">

            {{-- Tombol membuka dan menutup sidebar. --}}
            <button
                type="button"
                id="toggleSidebar"
                title="Buka atau tutup sidebar"
                aria-label="Buka atau tutup sidebar"
                class="inline-flex h-10 w-10 flex-shrink-0
                    items-center justify-center rounded-lg
                    border border-slate-200 text-slate-600
                    transition hover:bg-slate-100
                    hover:text-slate-800"
            >
                <i class="bi bi-list text-xl"></i>
            </button>

            {{-- Judul aplikasi. --}}
            <div class="min-w-0">
                <h1
                    class="truncate text-base font-bold
                        text-slate-800 sm:text-lg"
                >
                    Sistem Gudang
                </h1>

                <p
                    class="hidden truncate text-xs
                        text-slate-500 sm:block"
                >
                    Pengelolaan persediaan dan transaksi barang
                </p>
            </div>
        </div>

        {{-- Bagian kanan navbar. --}}
        <div class="flex flex-shrink-0 items-center gap-3">

            {{-- Informasi pengguna. --}}
            <div
                class="hidden items-center gap-3 border-r
                    border-slate-200 pr-4 md:flex"
            >
                {{-- Inisial pengguna. --}}
                <div
                    class="flex h-10 w-10 items-center
                        justify-center rounded-full bg-blue-100
                        text-sm font-bold text-blue-700"
                >
                    {{ $userInitial }}
                </div>

                {{-- Nama dan keterangan pengguna. --}}
                <div class="max-w-48">
                    <p
                        class="truncate text-sm font-semibold
                            text-slate-800"
                    >
                        {{ $authenticatedUser->name }}
                    </p>

                    <p class="truncate text-xs text-slate-500">
                        Pengguna Sistem
                    </p>
                </div>
            </div>

            {{-- Form logout. --}}
            <form
                method="POST"
                action="{{ route('logout') }}"
                data-confirm="Apakah Anda yakin ingin keluar dari aplikasi?"
                data-confirm-title="Konfirmasi Logout"
                data-confirm-type="warning"
                data-confirm-button="Ya, Keluar"
                data-cancel-button="Tetap Masuk"
            >
                @csrf

                <button
                    type="submit"
                    title="Keluar dari aplikasi"
                    class="inline-flex items-center justify-center gap-2
                        rounded-lg border border-red-200 bg-red-50
                        px-3 py-2.5 text-sm font-semibold text-red-700
                        transition hover:border-red-300 hover:bg-red-100
                        sm:px-4"
                >
                    <i class="bi bi-box-arrow-right text-base"></i>

                    <span class="hidden sm:inline">
                        Logout
                    </span>
                </button>
            </form>
        </div>
    </div>
</nav>
