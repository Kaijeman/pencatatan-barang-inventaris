@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">

        {{-- Header halaman dashboard. --}}
        <div
            class="flex flex-col gap-4 rounded-2xl border
                border-slate-200 bg-white p-6 shadow-sm
                sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <p
                    class="text-sm font-medium uppercase
                        tracking-wide text-blue-600"
                >
                    Ringkasan Gudang
                </p>

                <h1 class="mt-1 text-2xl font-bold text-slate-800">
                    Dashboard
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Pantau stok dan aktivitas gudang dalam satu halaman.
                </p>
            </div>

            {{-- Informasi pengguna aktif. --}}
            <div
                class="rounded-xl bg-slate-50 px-4 py-3
                    text-sm text-slate-600"
            >
                <div class="font-semibold text-slate-800">
                    {{ auth()->user()->name }}
                </div>

                <div>
                    Pengguna Sistem
                </div>

                <div class="mt-1 text-xs text-slate-500">
                    {{ now()->format('d/m/Y') }}
                </div>
            </div>
        </div>

        {{-- Kartu ringkasan data utama. --}}
        <div
            class="grid grid-cols-1 gap-4 sm:grid-cols-2
                xl:grid-cols-4"
        >
            <a
                href="{{ route('items.index') }}"
                class="group rounded-2xl border border-slate-200
                    bg-white p-5 shadow-sm transition
                    hover:-translate-y-0.5 hover:border-blue-300
                    hover:shadow-md"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Total Barang
                        </p>

                        <p
                            class="mt-2 text-3xl font-bold
                                text-slate-800"
                        >
                            {{ number_format($totalItems) }}
                        </p>
                    </div>

                    <div
                        class="flex h-11 w-11 items-center
                            justify-center rounded-xl bg-blue-100
                            text-xl text-blue-600"
                    >
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>

                <p
                    class="mt-4 text-xs font-medium text-blue-600
                        group-hover:underline"
                >
                    Lihat seluruh barang
                </p>
            </a>

            <a
                href="{{ route('categories.index') }}"
                class="group rounded-2xl border border-slate-200
                    bg-white p-5 shadow-sm transition
                    hover:-translate-y-0.5 hover:border-violet-300
                    hover:shadow-md"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Total Kategori
                        </p>

                        <p
                            class="mt-2 text-3xl font-bold
                                text-slate-800"
                        >
                            {{ number_format($totalCategories) }}
                        </p>
                    </div>

                    <div
                        class="flex h-11 w-11 items-center
                            justify-center rounded-xl bg-violet-100
                            text-xl text-violet-600"
                    >
                        <i class="bi bi-tags"></i>
                    </div>
                </div>

                <p
                    class="mt-4 text-xs font-medium text-violet-600
                        group-hover:underline"
                >
                    Lihat kategori
                </p>
            </a>

            <a
                href="{{ route('suppliers.index') }}"
                class="group rounded-2xl border border-slate-200
                    bg-white p-5 shadow-sm transition
                    hover:-translate-y-0.5 hover:border-emerald-300
                    hover:shadow-md"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Total Supplier
                        </p>

                        <p
                            class="mt-2 text-3xl font-bold
                                text-slate-800"
                        >
                            {{ number_format($totalSuppliers) }}
                        </p>
                    </div>

                    <div
                        class="flex h-11 w-11 items-center
                            justify-center rounded-xl bg-emerald-100
                            text-xl text-emerald-600"
                    >
                        <i class="bi bi-truck"></i>
                    </div>
                </div>

                <p
                    class="mt-4 text-xs font-medium text-emerald-600
                        group-hover:underline"
                >
                    Lihat supplier
                </p>
            </a>

            <a
                href="{{ route('reports.stock') }}"
                class="group rounded-2xl border border-slate-200
                    bg-white p-5 shadow-sm transition
                    hover:-translate-y-0.5 hover:border-cyan-300
                    hover:shadow-md"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Total Unit Stok
                        </p>

                        <p
                            class="mt-2 text-3xl font-bold
                                text-slate-800"
                        >
                            {{ number_format($totalStock) }}
                        </p>
                    </div>

                    <div
                        class="flex h-11 w-11 items-center
                            justify-center rounded-xl bg-cyan-100
                            text-xl text-cyan-600"
                    >
                        <i class="bi bi-boxes"></i>
                    </div>
                </div>

                <p
                    class="mt-4 text-xs font-medium text-cyan-600
                        group-hover:underline"
                >
                    Lihat laporan stok
                </p>
            </a>
        </div>

        {{-- Kartu kondisi stok dan transaksi hari ini. --}}
        <div
            class="grid grid-cols-1 gap-4 sm:grid-cols-2
                xl:grid-cols-4"
        >
            <div
                class="rounded-2xl border border-amber-200
                    bg-amber-50 p-5"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-amber-700">
                            Stok Menipis
                        </p>

                        <p
                            class="mt-2 text-3xl font-bold
                                text-amber-800"
                        >
                            {{ number_format($lowStockItems) }}
                        </p>
                    </div>

                    <div
                        class="flex h-11 w-11 items-center
                            justify-center rounded-xl bg-amber-100
                            text-xl text-amber-700"
                    >
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>

            <div
                class="rounded-2xl border border-red-200
                    bg-red-50 p-5"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-700">
                            Stok Habis
                        </p>

                        <p
                            class="mt-2 text-3xl font-bold
                                text-red-800"
                        >
                            {{ number_format($outOfStockItems) }}
                        </p>
                    </div>

                    <div
                        class="flex h-11 w-11 items-center
                            justify-center rounded-xl bg-red-100
                            text-xl text-red-700"
                    >
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>

            <div
                class="rounded-2xl border border-emerald-200
                    bg-emerald-50 p-5"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-emerald-700">
                            Barang Masuk Hari Ini
                        </p>

                        <p
                            class="mt-2 text-3xl font-bold
                                text-emerald-800"
                        >
                            {{ number_format($todayReceiptCount) }}
                        </p>
                    </div>

                    <div
                        class="flex h-11 w-11 items-center
                            justify-center rounded-xl bg-emerald-100
                            text-xl text-emerald-700"
                    >
                        <i class="bi bi-box-arrow-in-down"></i>
                    </div>
                </div>
            </div>

            <div
                class="rounded-2xl border border-orange-200
                    bg-orange-50 p-5"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-orange-700">
                            Barang Keluar Hari Ini
                        </p>

                        <p
                            class="mt-2 text-3xl font-bold
                                text-orange-800"
                        >
                            {{ number_format($todayIssueCount) }}
                        </p>
                    </div>

                    <div
                        class="flex h-11 w-11 items-center
                            justify-center rounded-xl bg-orange-100
                            text-xl text-orange-700"
                    >
                        <i class="bi bi-box-arrow-up"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol aksi cepat operasional. --}}
        <div
            class="rounded-2xl border border-slate-200
                bg-white p-6 shadow-sm"
        >
            <div>
                <h2 class="text-lg font-bold text-slate-800">
                    Aksi Cepat
                </h2>

                <p class="text-sm text-slate-500">
                    Akses langsung ke aktivitas utama gudang.
                </p>
            </div>

            <div
                class="mt-5 grid grid-cols-1 gap-3
                    sm:grid-cols-2 xl:grid-cols-3"
            >
                <a
                    href="{{ route('goods-receipts.create') }}"
                    class="flex items-center gap-3 rounded-xl
                        bg-emerald-600 px-4 py-3 font-semibold
                        text-white transition hover:bg-emerald-700"
                >
                    <i class="bi bi-box-arrow-in-down text-lg"></i>

                    <span>Catat Barang Masuk</span>
                </a>

                <a
                    href="{{ route('goods-issues.create') }}"
                    class="flex items-center gap-3 rounded-xl
                        bg-orange-600 px-4 py-3 font-semibold
                        text-white transition hover:bg-orange-700"
                >
                    <i class="bi bi-box-arrow-up text-lg"></i>

                    <span>Catat Barang Keluar</span>
                </a>

                <a
                    href="{{ route('items.create') }}"
                    class="flex items-center gap-3 rounded-xl
                        bg-blue-600 px-4 py-3 font-semibold
                        text-white transition hover:bg-blue-700"
                >
                    <i class="bi bi-plus-square text-lg"></i>

                    <span>Tambah Barang</span>
                </a>
            </div>
        </div>

        {{-- Daftar transaksi terbaru. --}}
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

            {{-- Barang masuk terbaru. --}}
            <div
                class="overflow-hidden rounded-2xl border
                    border-slate-200 bg-white shadow-sm"
            >
                <div
                    class="flex items-center justify-between
                        border-b border-slate-200 px-6 py-4"
                >
                    <div>
                        <h2 class="font-bold text-slate-800">
                            Barang Masuk Terbaru
                        </h2>

                        <p class="text-sm text-slate-500">
                            Lima transaksi terakhir.
                        </p>
                    </div>

                    <a
                        href="{{ route('goods-receipts.index') }}"
                        class="text-sm font-semibold text-blue-600
                            hover:underline"
                    >
                        Lihat Semua
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs
                                        font-semibold uppercase
                                        tracking-wide text-slate-500"
                                >
                                    Tanggal
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs
                                        font-semibold uppercase
                                        tracking-wide text-slate-500"
                                >
                                    Supplier
                                </th>

                                <th
                                    class="px-6 py-3 text-right text-xs
                                        font-semibold uppercase
                                        tracking-wide text-slate-500"
                                >
                                    Jumlah
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            class="divide-y divide-slate-100 bg-white"
                        >
                            @forelse ($recentReceipts as $receipt)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4">
                                        <a
                                            href="{{ route(
                                                'goods-receipts.show',
                                                $receipt
                                            ) }}"
                                            class="font-semibold
                                                text-blue-600
                                                hover:underline"
                                        >
                                            {{ $receipt
                                                ->received_at
                                                ?->format('d/m/Y') ?? '-' }}
                                        </a>

                                        <div
                                            class="mt-1 text-xs
                                                text-slate-500"
                                        >
                                            Dicatat oleh
                                            {{ $receipt->user?->name ?? '-' }}
                                        </div>
                                    </td>

                                    <td
                                        class="px-6 py-4 text-sm
                                            text-slate-700"
                                    >
                                        {{ $receipt
                                            ->supplier
                                            ?->name ?? '-' }}
                                    </td>

                                    <td
                                        class="px-6 py-4 text-right
                                            text-sm font-semibold
                                            text-slate-800"
                                    >
                                        {{ number_format(
                                            (int) (
                                                $receipt
                                                    ->details_sum_quantity
                                                ?? 0
                                            )
                                        ) }}
                                        unit
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="3"
                                        class="px-6 py-10 text-center
                                            text-sm text-slate-500"
                                    >
                                        Belum ada transaksi barang masuk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Barang keluar terbaru. --}}
            <div
                class="overflow-hidden rounded-2xl border
                    border-slate-200 bg-white shadow-sm"
            >
                <div
                    class="flex items-center justify-between
                        border-b border-slate-200 px-6 py-4"
                >
                    <div>
                        <h2 class="font-bold text-slate-800">
                            Barang Keluar Terbaru
                        </h2>

                        <p class="text-sm text-slate-500">
                            Lima transaksi terakhir.
                        </p>
                    </div>

                    <a
                        href="{{ route('goods-issues.index') }}"
                        class="text-sm font-semibold text-blue-600
                            hover:underline"
                    >
                        Lihat Semua
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs
                                        font-semibold uppercase
                                        tracking-wide text-slate-500"
                                >
                                    Tanggal
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs
                                        font-semibold uppercase
                                        tracking-wide text-slate-500"
                                >
                                    Tujuan
                                </th>

                                <th
                                    class="px-6 py-3 text-right text-xs
                                        font-semibold uppercase
                                        tracking-wide text-slate-500"
                                >
                                    Jumlah
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            class="divide-y divide-slate-100 bg-white"
                        >
                            @forelse ($recentIssues as $issue)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4">
                                        <a
                                            href="{{ route(
                                                'goods-issues.show',
                                                $issue
                                            ) }}"
                                            class="font-semibold
                                                text-blue-600
                                                hover:underline"
                                        >
                                            {{ $issue
                                                ->issued_at
                                                ?->format('d/m/Y') ?? '-' }}
                                        </a>

                                        <div
                                            class="mt-1 text-xs
                                                text-slate-500"
                                        >
                                            Dicatat oleh
                                            {{ $issue->user?->name ?? '-' }}
                                        </div>
                                    </td>

                                    <td
                                        class="px-6 py-4 text-sm
                                            text-slate-700"
                                    >
                                        {{ $issue->destination }}
                                    </td>

                                    <td
                                        class="px-6 py-4 text-right
                                            text-sm font-semibold
                                            text-slate-800"
                                    >
                                        {{ number_format(
                                            (int) (
                                                $issue
                                                    ->details_sum_quantity
                                                ?? 0
                                            )
                                        ) }}
                                        unit
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="3"
                                        class="px-6 py-10 text-center
                                            text-sm text-slate-500"
                                    >
                                        Belum ada transaksi barang keluar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Daftar barang yang membutuhkan perhatian. --}}
        <div
            class="overflow-hidden rounded-2xl border
                border-slate-200 bg-white shadow-sm"
        >
            <div
                class="flex flex-col gap-3 border-b border-slate-200
                    px-6 py-4 sm:flex-row sm:items-center
                    sm:justify-between"
            >
                <div>
                    <h2 class="font-bold text-slate-800">
                        Barang Memerlukan Perhatian
                    </h2>

                    <p class="text-sm text-slate-500">
                        Barang dengan stok menipis atau habis.
                    </p>
                </div>

                <a
                    href="{{ route('reports.stock', [
                        'stock_status' => 'low',
                    ]) }}"
                    class="text-sm font-semibold text-blue-600
                        hover:underline"
                >
                    Buka Laporan Stok
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs
                                    font-semibold uppercase
                                    tracking-wide text-slate-500"
                            >
                                Barang
                            </th>

                            <th
                                class="px-6 py-3 text-left text-xs
                                    font-semibold uppercase
                                    tracking-wide text-slate-500"
                            >
                                Kategori
                            </th>

                            <th
                                class="px-6 py-3 text-right text-xs
                                    font-semibold uppercase
                                    tracking-wide text-slate-500"
                            >
                                Stok
                            </th>

                            <th
                                class="px-6 py-3 text-right text-xs
                                    font-semibold uppercase
                                    tracking-wide text-slate-500"
                            >
                                Minimum
                            </th>

                            <th
                                class="px-6 py-3 text-center text-xs
                                    font-semibold uppercase
                                    tracking-wide text-slate-500"
                            >
                                Status
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($stockAttentionItems as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div
                                        class="font-semibold
                                            text-slate-800"
                                    >
                                        {{ $item->name }}
                                    </div>

                                    <div class="text-xs text-slate-500">
                                        {{ $item->unit }}
                                    </div>
                                </td>

                                <td
                                    class="px-6 py-4 text-sm
                                        text-slate-700"
                                >
                                    {{ $item->category?->name ?? '-' }}
                                </td>

                                <td
                                    class="px-6 py-4 text-right
                                        font-semibold text-slate-800"
                                >
                                    {{ number_format(
                                        (int) $item->stock
                                    ) }}
                                    {{ $item->unit }}
                                </td>

                                <td
                                    class="px-6 py-4 text-right
                                        text-sm text-slate-600"
                                >
                                    {{ number_format(
                                        (int) $item->minimum_stock
                                    ) }}
                                    {{ $item->unit }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if ((int) $item->stock <= 0)
                                        <span
                                            class="inline-flex
                                                rounded-full bg-red-100
                                                px-3 py-1 text-xs
                                                font-semibold text-red-700"
                                        >
                                            Stok Habis
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex
                                                rounded-full bg-amber-100
                                                px-3 py-1 text-xs
                                                font-semibold
                                                text-amber-700"
                                        >
                                            Stok Menipis
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="5"
                                    class="px-6 py-10 text-center"
                                >
                                    <div
                                        class="mx-auto flex h-12 w-12
                                            items-center justify-center
                                            rounded-full bg-emerald-100
                                            text-xl text-emerald-600"
                                    >
                                        <i class="bi bi-check-circle"></i>
                                    </div>

                                    <p
                                        class="mt-3 font-semibold
                                            text-slate-700"
                                    >
                                        Kondisi stok aman
                                    </p>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Tidak ada barang dengan stok
                                        menipis atau habis.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
