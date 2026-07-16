@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        {{-- Judul halaman --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Data Barang
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola data barang dan pantau jumlah stok gudang.
                </p>
            </div>

            {{-- Tombol tambah barang --}}
            <a href="{{ route('items.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">

                <i class="bi bi-plus-lg"></i>

                Tambah Barang
            </a>
        </div>

        {{-- Pesan berhasil --}}
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Pesan gagal --}}
        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-xl bg-white shadow-sm">

            {{-- Form filter barang --}}
            <div class="border-b border-slate-200 p-5">
                <form method="GET"
                      action="{{ route('items.index') }}"
                      class="grid grid-cols-1 gap-4 lg:grid-cols-5 lg:items-end">

                    {{-- Pencarian barang --}}
                    <div class="lg:col-span-2">
                        <label for="search"
                               class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Pencarian
                        </label>

                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Cari kode, nama, kategori, atau satuan..."
                               class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">

                        @error('search')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Filter kategori --}}
                    <div>
                        <label for="category_id"
                               class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Kategori
                        </label>

                        <select id="category_id"
                                name="category_id"
                                class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">

                            <option value="">
                                Semua kategori
                            </option>

                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    @selected(
                                        (string) $categoryId
                                        === (string) $category->id
                                    )>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Filter status stok --}}
                    <div>
                        <label for="stock_status"
                               class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Status Stok
                        </label>

                        <select id="stock_status"
                                name="stock_status"
                                class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">

                            <option value="">
                                Semua status
                            </option>

                            <option value="available"
                                @selected($stockStatus === 'available')>
                                Tersedia
                            </option>

                            <option value="low"
                                @selected($stockStatus === 'low')>
                                Menipis
                            </option>

                            <option value="out"
                                @selected($stockStatus === 'out')>
                                Habis
                            </option>
                        </select>

                        @error('stock_status')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Tombol filter --}}
                    <div class="flex gap-2">
                        <button type="submit"
                                class="flex-1 rounded-lg bg-slate-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Filter
                        </button>

                        <a href="{{ route('items.index') }}"
                           class="rounded-lg border border-slate-300 px-4 py-2.5 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                            Reset
                        </a>
                    </div>
                </form>

                {{-- Keterangan filter aktif --}}
                @if ($search !== '' || $categoryId || $stockStatus)
                    <div class="mt-4 rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                        Menampilkan barang berdasarkan filter yang dipilih.
                    </div>
                @endif
            </div>

            {{-- Tabel barang --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Kode
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Barang
                            </th>

                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-slate-500">
                                Harga
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase text-slate-500">
                                Stok
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase text-slate-500">
                                Minimum
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase text-slate-500">
                                Status
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($items as $item)
                            <tr class="transition hover:bg-slate-50">

                                {{-- Kode barang --}}
                                <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-700">
                                    {{ $item->code }}
                                </td>

                                {{-- Informasi barang --}}
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-800">
                                        {{ $item->name }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $item->category->name }}
                                        ·
                                        {{ $item->unit }}
                                    </p>
                                </td>

                                {{-- Harga beli --}}
                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm text-slate-600">
                                    Rp{{ number_format(
                                        $item->purchase_price,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                {{-- Stok saat ini --}}
                                <td class="whitespace-nowrap px-5 py-4 text-center text-sm font-semibold text-slate-700">
                                    {{ number_format($item->stock) }}
                                    {{ $item->unit }}
                                </td>

                                {{-- Stok minimum --}}
                                <td class="whitespace-nowrap px-5 py-4 text-center text-sm text-slate-600">
                                    {{ number_format($item->minimum_stock) }}
                                </td>

                                {{-- Status stok --}}
                                <td class="whitespace-nowrap px-5 py-4 text-center">
                                    @if ((int) $item->stock === 0)
                                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                            Habis
                                        </span>
                                    @elseif (
                                        (int) $item->stock
                                        <= (int) $item->minimum_stock
                                    )
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                            Menipis
                                        </span>
                                    @else
                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            Tersedia
                                        </span>
                                    @endif
                                </td>

                                {{-- Tombol aksi --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex justify-center gap-2">

                                        {{-- Tombol edit --}}
                                        <a href="{{ route('items.edit', $item) }}"
                                           title="Edit barang"
                                           class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-700 transition hover:bg-amber-200">

                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        {{-- Form hapus barang --}}
                                        <form method="POST"
                                              action="{{ route('items.destroy', $item) }}"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    title="Hapus barang"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-100 text-red-700 transition hover:bg-red-200">

                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7"
                                    class="px-6 py-12 text-center">

                                    <i class="bi bi-box-seam text-4xl text-slate-300"></i>

                                    <p class="mt-3 font-medium text-slate-600">
                                        Tidak ada data barang yang sesuai.
                                    </p>

                                    <p class="mt-1 text-sm text-slate-400">
                                        Tambahkan barang atau ubah filter pencarian.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($items->hasPages())
                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
