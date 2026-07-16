@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        {{-- Judul halaman --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Laporan Stok
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Pantau jumlah dan nilai persediaan barang saat ini.
                </p>
            </div>

            <a href="{{ route(
                    'reports.stock.export',
                    request()->except('page')
                ) }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-700">

                <i class="bi bi-file-earmark-spreadsheet"></i>

                Export CSV
            </a>
        </div>

        {{-- Ringkasan laporan --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-5">

            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Total Jenis Barang
                </p>

                <p class="mt-2 text-2xl font-bold text-slate-800">
                    {{ number_format($summary['total_items']) }}
                </p>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Total Seluruh Stok
                </p>

                <p class="mt-2 text-2xl font-bold text-slate-800">
                    {{ number_format($summary['total_stock']) }}
                </p>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Nilai Persediaan
                </p>

                <p class="mt-2 text-xl font-bold text-slate-800">
                    Rp{{ number_format(
                        $summary['total_value'],
                        0,
                        ',',
                        '.'
                    ) }}
                </p>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Stok Menipis
                </p>

                <p class="mt-2 text-2xl font-bold text-amber-600">
                    {{ number_format(
                        $summary['low_stock_items']
                    ) }}
                </p>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Stok Habis
                </p>

                <p class="mt-2 text-2xl font-bold text-red-600">
                    {{ number_format(
                        $summary['out_of_stock_items']
                    ) }}
                </p>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm">

            {{-- Filter laporan --}}
            <div class="border-b border-slate-200 p-5">
                <form method="GET"
                      action="{{ route('reports.stock') }}"
                      class="grid grid-cols-1 gap-3 lg:grid-cols-5">

                    <div class="lg:col-span-2">
                        <input type="text"
                               name="search"
                               value="{{ $filters['search'] }}"
                               placeholder="Cari kode, nama, kategori, atau satuan..."
                               class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>

                    <select name="category_id"
                            class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">

                        <option value="">
                            Semua kategori
                        </option>

                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                @selected(
                                    (string) $filters['category_id']
                                    === (string) $category->id
                                )>

                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="stock_status"
                            class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">

                        <option value="">
                            Semua status stok
                        </option>

                        <option value="available"
                            @selected(
                                $filters['stock_status']
                                === 'available'
                            )>
                            Tersedia
                        </option>

                        <option value="low"
                            @selected(
                                $filters['stock_status']
                                === 'low'
                            )>
                            Menipis
                        </option>

                        <option value="out"
                            @selected(
                                $filters['stock_status']
                                === 'out'
                            )>
                            Habis
                        </option>
                    </select>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="flex-1 rounded-lg bg-slate-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Filter
                        </button>

                        <a href="{{ route('reports.stock') }}"
                           class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tabel laporan stok --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">

                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                No.
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Barang
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Kategori
                            </th>

                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-slate-500">
                                Harga Beli
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

                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-slate-500">
                                Nilai Persediaan
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($items as $item)
                            <tr class="transition hover:bg-slate-50">

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-500">
                                    {{ ($items->currentPage() - 1)
                                        * $items->perPage()
                                        + $loop->iteration }}
                                </td>

                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-800">
                                        {{ $item->name }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        {{ $item->code }}
                                        ·
                                        {{ $item->unit }}
                                    </p>
                                </td>

                                <td class="px-5 py-4 text-sm text-slate-600">
                                    {{ $item->category->name }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm text-slate-600">
                                    Rp{{ number_format(
                                        $item->purchase_price,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-center text-sm font-semibold text-slate-800">
                                    {{ number_format($item->stock) }}
                                    {{ $item->unit }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-center text-sm text-slate-600">
                                    {{ number_format(
                                        $item->minimum_stock
                                    ) }}
                                </td>

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

                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-slate-800">
                                    Rp{{ number_format(
                                        $item->stock
                                            * $item->purchase_price,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8"
                                    class="px-6 py-12 text-center">

                                    <i class="bi bi-clipboard-data text-4xl text-slate-300"></i>

                                    <p class="mt-3 font-medium text-slate-600">
                                        Tidak ada data stok yang sesuai.
                                    </p>

                                    <p class="mt-1 text-sm text-slate-400">
                                        Coba ubah atau reset filter laporan.
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
