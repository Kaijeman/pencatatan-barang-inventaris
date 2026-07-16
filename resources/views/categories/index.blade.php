@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        {{-- Judul halaman --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Data Kategori
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola kategori yang digunakan untuk mengelompokkan barang.
                </p>
            </div>

            {{-- Tombol tambah kategori --}}
            <a href="{{ route('categories.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">

                <i class="bi bi-plus-lg"></i>

                Tambah Kategori
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

            {{-- Form pencarian kategori --}}
            <div class="border-b border-slate-200 p-5">
                <form method="GET"
                      action="{{ route('categories.index') }}"
                      class="grid grid-cols-1 gap-4 sm:grid-cols-[1fr_auto] sm:items-end">

                    {{-- Input pencarian --}}
                    <div>
                        <label for="search"
                               class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Pencarian
                        </label>

                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Cari nama atau deskripsi kategori..."
                               class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">

                        @error('search')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Tombol pencarian --}}
                    <div class="flex gap-2">
                        <button type="submit"
                                class="rounded-lg bg-slate-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Cari
                        </button>

                        <a href="{{ route('categories.index') }}"
                           class="rounded-lg border border-slate-300 px-5 py-2.5 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                            Reset
                        </a>
                    </div>
                </form>

                {{-- Keterangan pencarian aktif --}}
                @if ($search !== '')
                    <div class="mt-4 rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                        Menampilkan kategori berdasarkan pencarian:

                        <span class="font-semibold">
                            {{ $search }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Tabel kategori --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="w-20 px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                No.
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Nama Kategori
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Deskripsi
                            </th>

                            <th class="w-32 px-5 py-3 text-center text-xs font-semibold uppercase text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($categories as $category)
                            <tr class="transition hover:bg-slate-50">

                                {{-- Nomor urut --}}
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-500">
                                    {{ ($categories->currentPage() - 1)
                                        * $categories->perPage()
                                        + $loop->iteration }}
                                </td>

                                {{-- Nama kategori --}}
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-800">
                                        {{ $category->name }}
                                    </p>
                                </td>

                                {{-- Deskripsi kategori --}}
                                <td class="px-5 py-4 text-sm text-slate-600">
                                    {{ $category->description ?: '-' }}
                                </td>

                                {{-- Tombol aksi --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex justify-center gap-2">

                                        {{-- Tombol edit --}}
                                        <a href="{{ route('categories.edit', $category) }}"
                                           title="Edit kategori"
                                           class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-700 transition hover:bg-amber-200">

                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        {{-- Form hapus kategori --}}
                                        <form method="POST"
                                              action="{{ route('categories.destroy', $category) }}"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    title="Hapus kategori"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-100 text-red-700 transition hover:bg-red-200">

                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="px-6 py-12 text-center">

                                    <i class="bi bi-tags text-4xl text-slate-300"></i>

                                    <p class="mt-3 font-medium text-slate-600">
                                        Belum ada data kategori.
                                    </p>

                                    <p class="mt-1 text-sm text-slate-400">
                                        Tambahkan kategori untuk mengelompokkan barang.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($categories->hasPages())
                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
