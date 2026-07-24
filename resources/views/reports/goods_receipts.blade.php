@extends('layouts.app')

@section('title', 'Laporan Barang Masuk')

@section('content')
    <div class="space-y-6">

        {{-- Judul halaman. --}}
        <div
            class="flex flex-col gap-4 lg:flex-row
                lg:items-center lg:justify-between"
        >
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Laporan Barang Masuk
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Rekap penerimaan barang dari supplier
                    berdasarkan periode.
                </p>
            </div>

            {{-- Tombol ekspor laporan. --}}
            <a
                href="{{ route(
                    'reports.goods-receipts.export',
                    request()->except('page')
                ) }}"
                class="inline-flex items-center justify-center
                    gap-2 rounded-lg bg-green-600 px-4 py-2.5
                    text-sm font-semibold text-white transition
                    hover:bg-green-700"
            >
                <i class="bi bi-file-earmark-spreadsheet"></i>

                Export CSV
            </a>
        </div>

        {{-- Ringkasan laporan. --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">

            {{-- Total transaksi. --}}
            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Total Transaksi
                </p>

                <p class="mt-2 text-2xl font-bold text-slate-800">
                    {{ number_format(
                        $summary['total_transactions']
                    ) }}
                </p>
            </div>

            {{-- Total kuantitas barang masuk. --}}
            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Total Barang Masuk
                </p>

                <p class="mt-2 text-2xl font-bold text-blue-600">
                    {{ number_format(
                        $summary['total_quantity']
                    ) }}
                </p>
            </div>

            {{-- Total nilai pembelian. --}}
            <div class="rounded-xl bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Total Nilai Pembelian
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
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm">

            {{-- Form filter laporan. --}}
            <div class="border-b border-slate-200 p-5">
                <form
                    method="GET"
                    action="{{ route(
                        'reports.goods-receipts'
                    ) }}"
                    class="grid grid-cols-1 gap-4
                        xl:grid-cols-6 xl:items-end"
                >
                    {{-- Pencarian. --}}
                    <div class="xl:col-span-2">
                        <label
                            for="search"
                            class="mb-2 block text-xs font-semibold
                                uppercase tracking-wide text-slate-500"
                        >
                            Pencarian
                        </label>

                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ $filters['search'] }}"
                            placeholder="Cari supplier, barang, kategori, petugas, atau catatan..."
                            class="w-full rounded-lg border
                                border-slate-300 px-4 py-2.5 text-sm
                                outline-none transition
                                focus:border-blue-500 focus:ring-2
                                focus:ring-blue-200"
                        >

                        @error('search')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Filter supplier. --}}
                    <div>
                        <label
                            for="supplier_id"
                            class="mb-2 block text-xs font-semibold
                                uppercase tracking-wide text-slate-500"
                        >
                            Supplier
                        </label>

                        <select
                            id="supplier_id"
                            name="supplier_id"
                            class="w-full rounded-lg border
                                border-slate-300 px-4 py-2.5 text-sm
                                outline-none transition
                                focus:border-blue-500 focus:ring-2
                                focus:ring-blue-200"
                        >
                            <option value="">
                                Semua supplier
                            </option>

                            @foreach ($suppliers as $supplier)
                                <option
                                    value="{{ $supplier->id }}"
                                    @selected(
                                        (string)
                                            $filters['supplier_id']
                                        === (string)
                                            $supplier->id
                                    )
                                >
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('supplier_id')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Filter tanggal awal. --}}
                    <div>
                        <label
                            for="start_date"
                            class="mb-2 block text-xs font-semibold
                                uppercase tracking-wide text-slate-500"
                        >
                            Tanggal Awal
                        </label>

                        <input
                            type="date"
                            id="start_date"
                            name="start_date"
                            value="{{ $filters['start_date'] }}"
                            max="{{ now()->format('Y-m-d') }}"
                            class="w-full rounded-lg border
                                border-slate-300 px-4 py-2.5 text-sm
                                outline-none transition
                                focus:border-blue-500 focus:ring-2
                                focus:ring-blue-200"
                        >

                        @error('start_date')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Filter tanggal akhir. --}}
                    <div>
                        <label
                            for="end_date"
                            class="mb-2 block text-xs font-semibold
                                uppercase tracking-wide text-slate-500"
                        >
                            Tanggal Akhir
                        </label>

                        <input
                            type="date"
                            id="end_date"
                            name="end_date"
                            value="{{ $filters['end_date'] }}"
                            max="{{ now()->format('Y-m-d') }}"
                            class="w-full rounded-lg border
                                border-slate-300 px-4 py-2.5 text-sm
                                outline-none transition
                                focus:border-blue-500 focus:ring-2
                                focus:ring-blue-200"
                        >

                        @error('end_date')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Tombol filter. --}}
                    <div class="flex gap-2">
                        <button
                            type="submit"
                            class="flex-1 rounded-lg bg-slate-700
                                px-4 py-2.5 text-sm font-semibold
                                text-white transition
                                hover:bg-slate-800"
                        >
                            Filter
                        </button>

                        <a
                            href="{{ route(
                                'reports.goods-receipts'
                            ) }}"
                            class="rounded-lg border border-slate-300
                                px-4 py-2.5 text-center text-sm
                                font-semibold text-slate-600
                                transition hover:bg-slate-50"
                        >
                            Reset
                        </a>
                    </div>
                </form>

                {{-- Keterangan periode. --}}
                @if (
                    $filters['start_date']
                    || $filters['end_date']
                )
                    <div
                        class="mt-4 rounded-lg border border-blue-100
                            bg-blue-50 px-4 py-3 text-sm
                            text-blue-700"
                    >
                        Periode laporan:

                        <span class="font-semibold">
                            {{ $filters['start_date']
                                ? \Carbon\Carbon::parse(
                                    $filters['start_date']
                                )->format('d/m/Y')
                                : 'Awal data' }}
                        </span>

                        sampai

                        <span class="font-semibold">
                            {{ $filters['end_date']
                                ? \Carbon\Carbon::parse(
                                    $filters['end_date']
                                )->format('d/m/Y')
                                : 'Hari ini' }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Tabel laporan barang masuk. --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th
                                class="px-5 py-3 text-left text-xs
                                    font-semibold uppercase
                                    text-slate-500"
                            >
                                Tanggal
                            </th>

                            <th
                                class="px-5 py-3 text-left text-xs
                                    font-semibold uppercase
                                    text-slate-500"
                            >
                                Supplier
                            </th>

                            <th
                                class="px-5 py-3 text-center text-xs
                                    font-semibold uppercase
                                    text-slate-500"
                            >
                                Jenis Barang
                            </th>

                            <th
                                class="px-5 py-3 text-center text-xs
                                    font-semibold uppercase
                                    text-slate-500"
                            >
                                Jumlah
                            </th>

                            <th
                                class="px-5 py-3 text-right text-xs
                                    font-semibold uppercase
                                    text-slate-500"
                            >
                                Total Nilai
                            </th>

                            <th
                                class="px-5 py-3 text-left text-xs
                                    font-semibold uppercase
                                    text-slate-500"
                            >
                                Petugas
                            </th>

                            <th
                                class="px-5 py-3 text-center text-xs
                                    font-semibold uppercase
                                    text-slate-500"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($receipts as $receipt)
                            <tr class="transition hover:bg-slate-50">

                                {{-- Tanggal transaksi. --}}
                                <td
                                    class="whitespace-nowrap px-5
                                        py-4 text-sm font-semibold
                                        text-slate-800"
                                >
                                    {{ $receipt
                                        ->received_at
                                        ?->format('d/m/Y') ?? '-' }}
                                </td>

                                {{-- Supplier. --}}
                                <td
                                    class="px-5 py-4 text-sm
                                        text-slate-700"
                                >
                                    {{ $receipt
                                        ->supplier
                                        ?->name ?? '-' }}
                                </td>

                                {{-- Jumlah jenis barang. --}}
                                <td
                                    class="whitespace-nowrap px-5
                                        py-4 text-center text-sm
                                        text-slate-600"
                                >
                                    {{ number_format(
                                        (int)
                                            $receipt->details_count
                                    ) }}
                                </td>

                                {{-- Total kuantitas. --}}
                                <td
                                    class="whitespace-nowrap px-5
                                        py-4 text-center text-sm
                                        font-semibold text-slate-700"
                                >
                                    {{ number_format(
                                        (int) (
                                            $receipt
                                                ->details_sum_quantity
                                            ?? 0
                                        )
                                    ) }}
                                </td>

                                {{-- Total nilai transaksi. --}}
                                <td
                                    class="whitespace-nowrap px-5
                                        py-4 text-right text-sm
                                        font-semibold text-slate-800"
                                >
                                    Rp{{ number_format(
                                        (float) (
                                            $receipt->total_value
                                            ?? 0
                                        ),
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                {{-- Petugas pencatat. --}}
                                <td
                                    class="px-5 py-4 text-sm
                                        text-slate-600"
                                >
                                    {{ $receipt->user?->name ?? '-' }}
                                </td>

                                {{-- Tombol detail. --}}
                                <td
                                    class="whitespace-nowrap px-5
                                        py-4 text-center"
                                >
                                    <a
                                        href="{{ route(
                                            'goods-receipts.show',
                                            $receipt
                                        ) }}"
                                        title="Lihat detail"
                                        class="inline-flex h-9 w-9
                                            items-center justify-center
                                            rounded-lg bg-blue-100
                                            text-blue-700 transition
                                            hover:bg-blue-200"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="7"
                                    class="px-6 py-12 text-center"
                                >
                                    <i
                                        class="bi bi-inbox text-4xl
                                            text-slate-300"
                                    ></i>

                                    <p
                                        class="mt-3 font-medium
                                            text-slate-600"
                                    >
                                        Tidak ada transaksi barang
                                        masuk yang sesuai.
                                    </p>

                                    <p
                                        class="mt-1 text-sm
                                            text-slate-400"
                                    >
                                        Coba ubah periode atau reset
                                        filter.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination. --}}
            @if ($receipts->hasPages())
                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $receipts->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
