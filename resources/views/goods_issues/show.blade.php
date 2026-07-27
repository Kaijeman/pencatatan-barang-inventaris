@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">

        {{-- Judul halaman. --}}
        <div
            class="flex flex-col gap-4 sm:flex-row
                sm:items-center sm:justify-between"
        >
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Detail Barang Keluar
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Transaksi tanggal
                    {{ $goodsIssue->issued_at?->format('d/m/Y') ?? '-' }}
                </p>
            </div>

            <a
                href="{{ route('goods-issues.index') }}"
                class="rounded-lg border border-slate-300 bg-white
                    px-4 py-2.5 text-center text-sm font-semibold
                    text-slate-600 transition hover:bg-slate-50"
            >
                Kembali
            </a>
        </div>

        {{-- Pesan berhasil. --}}
        @if (session('success'))
            <div
                class="rounded-lg border border-green-200
                    bg-green-50 px-4 py-3 text-sm text-green-700"
            >
                {{ session('success') }}
            </div>
        @endif

        {{-- Pesan gagal. --}}
        @if (session('error'))
            <div
                class="rounded-lg border border-red-200
                    bg-red-50 px-4 py-3 text-sm text-red-700"
            >
                {{ session('error') }}
            </div>
        @endif

        {{-- Informasi transaksi. --}}
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <div
                class="grid grid-cols-1 gap-6
                    sm:grid-cols-2 lg:grid-cols-3"
            >
                {{-- Tanggal pengeluaran. --}}
                <div>
                    <p
                        class="text-xs font-semibold uppercase
                            text-slate-400"
                    >
                        Tanggal
                    </p>

                    <p class="mt-2 font-semibold text-slate-800">
                        {{ $goodsIssue->issued_at?->format(
                            'd/m/Y'
                        ) ?? '-' }}
                    </p>
                </div>

                {{-- Tujuan pengeluaran. --}}
                <div>
                    <p
                        class="text-xs font-semibold uppercase
                            text-slate-400"
                    >
                        Tujuan
                    </p>

                    <p class="mt-2 font-semibold text-slate-800">
                        {{ $goodsIssue->destination }}
                    </p>
                </div>

                {{-- Pengguna pencatat transaksi. --}}
                <div>
                    <p
                        class="text-xs font-semibold uppercase
                            text-slate-400"
                    >
                        Dicatat Oleh
                    </p>

                    <p class="mt-2 font-semibold text-slate-800">
                        {{ $goodsIssue->user?->name ?? '-' }}
                    </p>
                </div>
            </div>

            {{-- Catatan transaksi. --}}
            @if ($goodsIssue->note)
                <div class="mt-6 border-t border-slate-200 pt-5">
                    <p
                        class="text-xs font-semibold uppercase
                            text-slate-400"
                    >
                        Catatan
                    </p>

                    <p
                        class="mt-2 whitespace-pre-line text-sm
                            text-slate-600"
                    >
                        {{ $goodsIssue->note }}
                    </p>
                </div>
            @endif
        </div>

        {{-- Detail barang. --}}
        <div class="overflow-hidden rounded-xl bg-white shadow-sm">
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
                                    font-semibold uppercase
                                    text-slate-500"
                            >
                                Barang
                            </th>

                            <th
                                class="px-5 py-3 text-left text-xs
                                    font-semibold uppercase
                                    text-slate-500"
                            >
                                Kategori
                            </th>

                            <th
                                class="px-5 py-3 text-center text-xs
                                    font-semibold uppercase
                                    text-slate-500"
                            >
                                Jumlah Keluar
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($goodsIssue->details as $detail)
                            <tr class="transition hover:bg-slate-50">

                                {{-- Informasi barang. --}}
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-800">
                                        {{ $detail->item?->name ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Satuan:
                                        {{ $detail->item?->unit ?? '-' }}
                                    </p>
                                </td>

                                {{-- Kategori barang. --}}
                                <td
                                    class="px-5 py-4 text-sm
                                        text-slate-600"
                                >
                                    {{ $detail
                                        ->item
                                        ?->category
                                        ?->name ?? '-' }}
                                </td>

                                {{-- Jumlah barang keluar. --}}
                                <td
                                    class="whitespace-nowrap px-5 py-4
                                        text-center text-sm font-semibold
                                        text-slate-700"
                                >
                                    {{ number_format(
                                        (int) $detail->quantity
                                    ) }}

                                    {{ $detail->item?->unit ?? '' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="3"
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
                                        Tidak ada detail barang keluar.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot class="bg-slate-50">
                        <tr>
                            <td
                                colspan="2"
                                class="px-5 py-4 text-right text-sm
                                    font-semibold text-slate-600"
                            >
                                Total Barang Keluar
                            </td>

                            <td
                                class="px-5 py-4 text-center text-sm
                                    font-bold text-slate-800"
                            >
                                {{ number_format(
                                    (int) $totalQuantity
                                ) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
