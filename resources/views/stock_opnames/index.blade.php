@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        {{-- Judul halaman --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Stock Opname
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola pemeriksaan dan penyesuaian stok fisik gudang.
                </p>
            </div>

            <a href="{{ route('stock-opnames.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">

                <i class="bi bi-plus-lg"></i>

                Tambah Stock Opname
            </a>
        </div>

        {{-- Pesan berhasil --}}
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-xl bg-white shadow-sm">

            {{-- Pencarian dan filter --}}
            <div class="border-b border-slate-200 p-5">
                <form method="GET"
                      action="{{ route('stock-opnames.index') }}"
                      class="grid grid-cols-1 gap-3 lg:grid-cols-5">

                    <div class="lg:col-span-2">
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Cari kode, nama barang, atau petugas..."
                               class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>

                    <input type="date"
                           name="date"
                           value="{{ $date }}"
                           class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">

                    <select name="difference_status"
                            class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">

                        <option value="">Semua selisih</option>

                        <option value="positive"
                            @selected($differenceStatus === 'positive')>
                            Stok fisik lebih banyak
                        </option>

                        <option value="negative"
                            @selected($differenceStatus === 'negative')>
                            Stok fisik lebih sedikit
                        </option>

                        <option value="same"
                            @selected($differenceStatus === 'same')>
                            Tidak ada selisih
                        </option>
                    </select>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="flex-1 rounded-lg bg-slate-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Filter
                        </button>

                        <a href="{{ route('stock-opnames.index') }}"
                           class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tabel stock opname --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Tanggal
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Barang
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase text-slate-500">
                                Stok Sistem
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase text-slate-500">
                                Stok Fisik
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase text-slate-500">
                                Selisih
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Petugas
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($stockOpnames as $stockOpname)
                            <tr class="transition hover:bg-slate-50">
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                    {{ $stockOpname->opname_date->format('d/m/Y') }}
                                </td>

                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-800">
                                        {{ $stockOpname->item->name }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        {{ $stockOpname->item->code }}
                                        ·
                                        {{ $stockOpname->item->category->name }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-center text-sm text-slate-600">
                                    {{ $stockOpname->system_stock }}
                                    {{ $stockOpname->item->unit }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-center text-sm font-semibold text-slate-700">
                                    {{ $stockOpname->physical_stock }}
                                    {{ $stockOpname->item->unit }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-center">
                                    @if ($stockOpname->difference > 0)
                                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                            +{{ $stockOpname->difference }}
                                        </span>
                                    @elseif ($stockOpname->difference < 0)
                                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                            {{ $stockOpname->difference }}
                                        </span>
                                    @else
                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            Sesuai
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-sm text-slate-600">
                                    {{ $stockOpname->user->name }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-center">
                                    <a href="{{ route('stock-opnames.show', $stockOpname) }}"
                                       title="Lihat detail"
                                       class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 text-blue-700 transition hover:bg-blue-200">

                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7"
                                    class="px-6 py-12 text-center text-slate-500">

                                    <i class="bi bi-clipboard-check text-4xl text-slate-300"></i>

                                    <p class="mt-3 font-medium text-slate-600">
                                        Belum ada riwayat stock opname.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($stockOpnames->hasPages())
                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $stockOpnames->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
