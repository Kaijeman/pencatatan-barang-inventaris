@extends('layouts.app')

@section('title', 'Detail Barang Masuk')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        {{-- Kepala halaman. --}}
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <h1
                    class="text-xl font-bold text-slate-800
                        sm:text-2xl"
                >
                    Detail Barang Masuk
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Informasi lengkap transaksi penerimaan barang.
                </p>
            </div>

            {{-- Tombol kembali. --}}
            <a
                href="{{ route('goods-receipts.index') }}"
                title="Kembali ke barang masuk"
                aria-label="Kembali ke barang masuk"
                class="inline-flex h-10 w-10 flex-shrink-0
                    items-center justify-center rounded-lg
                    border border-slate-300 bg-white
                    text-slate-600 transition hover:bg-slate-50
                    hover:text-slate-800 sm:h-auto sm:w-auto
                    sm:gap-2 sm:px-4 sm:py-2.5 sm:text-sm
                    sm:font-semibold"
            >
                <i class="bi bi-arrow-left text-lg sm:text-base"></i>

                <span class="hidden sm:inline">
                    Kembali
                </span>
            </a>
        </div>

        {{-- Informasi transaksi. --}}
        <div class="rounded-xl bg-white p-4 shadow-sm sm:p-6">
            <h2 class="text-lg font-semibold text-slate-800">
                Informasi Penerimaan
            </h2>

            <div
                class="mt-5 grid grid-cols-1 gap-5
                    sm:grid-cols-2 lg:grid-cols-4"
            >
                <div>
                    <p class="text-xs font-semibold uppercase text-slate-500">
                        Tanggal
                    </p>

                    <p class="mt-2 text-sm font-semibold text-slate-800">
                        {{ $goodsReceipt->received_at?->format(
                            'd/m/Y'
                        ) ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-500">
                        Supplier
                    </p>

                    <p class="mt-2 text-sm font-semibold text-slate-800">
                        {{ $goodsReceipt->supplier?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-500">
                        Dicatat Oleh
                    </p>

                    <p class="mt-2 text-sm font-semibold text-slate-800">
                        {{ $goodsReceipt->recorded_by_name
                            ?? $goodsReceipt->user?->name
                            ?? 'Pengguna tidak diketahui' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-500">
                        Total Barang
                    </p>

                    <p class="mt-2 text-sm font-semibold text-slate-800">
                        {{ number_format((int) $totalQuantity) }} unit
                    </p>
                </div>
            </div>

            {{-- Catatan transaksi. --}}
            <div class="mt-6 border-t border-slate-200 pt-5">
                <p class="text-xs font-semibold uppercase text-slate-500">
                    Catatan
                </p>

                <p class="mt-2 whitespace-pre-line text-sm text-slate-700">
                    {{ $goodsReceipt->note ?: 'Tidak ada catatan.' }}
                </p>
            </div>
        </div>

        {{-- Detail barang desktop. --}}
        <div class="hidden overflow-hidden rounded-xl bg-white shadow-sm md:block">
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 class="text-lg font-semibold text-slate-800">
                    Daftar Barang
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th
                                class="px-5 py-3 text-left text-xs
                                    font-semibold uppercase text-slate-500"
                            >
                                Barang
                            </th>

                            <th
                                class="px-5 py-3 text-left text-xs
                                    font-semibold uppercase text-slate-500"
                            >
                                Kategori
                            </th>

                            <th
                                class="px-5 py-3 text-center text-xs
                                    font-semibold uppercase text-slate-500"
                            >
                                Jumlah
                            </th>

                            <th
                                class="px-5 py-3 text-right text-xs
                                    font-semibold uppercase text-slate-500"
                            >
                                Harga Beli
                            </th>

                            <th
                                class="px-5 py-3 text-right text-xs
                                    font-semibold uppercase text-slate-500"
                            >
                                Subtotal
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($goodsReceipt->details as $detail)
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-800">
                                        {{ $detail->item?->name ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $detail->item?->unit ?? '-' }}
                                    </p>
                                </td>

                                <td class="px-5 py-4 text-sm text-slate-600">
                                    {{ $detail->item?->category?->name
                                        ?? '-' }}
                                </td>

                                <td
                                    class="px-5 py-4 text-center
                                        text-sm font-semibold text-slate-800"
                                >
                                    {{ number_format(
                                        (int) $detail->quantity
                                    ) }}
                                </td>

                                <td
                                    class="px-5 py-4 text-right
                                        text-sm text-slate-700"
                                >
                                    Rp{{ number_format(
                                        (float) $detail->purchase_price,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                <td
                                    class="px-5 py-4 text-right
                                        text-sm font-semibold text-slate-800"
                                >
                                    Rp{{ number_format(
                                        (float) (
                                            $detail->quantity
                                            * $detail->purchase_price
                                        ),
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot class="bg-slate-50">
                        <tr>
                            <td
                                colspan="4"
                                class="px-5 py-4 text-right
                                    text-sm font-semibold text-slate-700"
                            >
                                Total Nilai
                            </td>

                            <td
                                class="px-5 py-4 text-right
                                    text-base font-bold text-slate-900"
                            >
                                Rp{{ number_format(
                                    (float) $totalValue,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Detail barang mobile. --}}
        <div class="space-y-4 md:hidden">
            <h2 class="text-lg font-semibold text-slate-800">
                Daftar Barang
            </h2>

            @foreach ($goodsReceipt->details as $detail)
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <h3 class="font-semibold text-slate-800">
                        {{ $detail->item?->name ?? '-' }}
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $detail->item?->category?->name ?? '-' }}
                    </p>

                    <div
                        class="mt-4 grid grid-cols-2 gap-4
                            border-t border-slate-200 pt-4"
                    >
                        <div>
                            <p class="text-xs uppercase text-slate-500">
                                Jumlah
                            </p>

                            <p class="mt-1 text-sm font-semibold">
                                {{ number_format(
                                    (int) $detail->quantity
                                ) }}
                                {{ $detail->item?->unit ?? '' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs uppercase text-slate-500">
                                Harga Beli
                            </p>

                            <p class="mt-1 text-sm font-semibold">
                                Rp{{ number_format(
                                    (float) $detail->purchase_price,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 rounded-lg bg-slate-50 p-3">
                        <p class="text-xs uppercase text-slate-500">
                            Subtotal
                        </p>

                        <p class="mt-1 font-bold text-slate-800">
                            Rp{{ number_format(
                                (float) (
                                    $detail->quantity
                                    * $detail->purchase_price
                                ),
                                0,
                                ',',
                                '.'
                            ) }}
                        </p>
                    </div>
                </div>
            @endforeach

            <div
                class="flex items-center justify-between
                    rounded-xl bg-slate-800 p-4 text-white"
            >
                <span class="text-sm font-semibold">
                    Total Nilai
                </span>

                <span class="text-lg font-bold">
                    Rp{{ number_format(
                        (float) $totalValue,
                        0,
                        ',',
                        '.'
                    ) }}
                </span>
            </div>
        </div>
    </div>
@endsection
