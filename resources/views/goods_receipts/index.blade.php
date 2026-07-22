@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        {{-- Judul halaman. --}}
        <div
            class="flex flex-col gap-4 sm:flex-row
                sm:items-center sm:justify-between"
        >
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Barang Masuk
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola riwayat penerimaan barang dari supplier.
                </p>
            </div>

            {{-- Tombol tambah barang masuk. --}}
            <a
                href="{{ route('goods-receipts.create') }}"
                class="inline-flex items-center justify-center gap-2
                    rounded-lg bg-blue-600 px-4 py-2.5 text-sm
                    font-semibold text-white transition
                    hover:bg-blue-700"
            >
                <i class="bi bi-plus-lg"></i>

                Tambah Barang Masuk
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

        <div class="overflow-hidden rounded-xl bg-white shadow-sm">

            {{-- Form filter transaksi barang masuk. --}}
            <div class="border-b border-slate-200 p-5">
                <form
                    method="GET"
                    action="{{ route('goods-receipts.index') }}"
                    class="grid grid-cols-1 gap-4
                        lg:grid-cols-4 lg:items-end"
                >
                    {{-- Pencarian transaksi. --}}
                    <div class="lg:col-span-2">
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
                            value="{{ $search }}"
                            placeholder="Cari supplier, petugas, atau catatan..."
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

                    {{-- Filter tanggal penerimaan. --}}
                    <div>
                        <label
                            for="date"
                            class="mb-2 block text-xs font-semibold
                                uppercase tracking-wide text-slate-500"
                        >
                            Tanggal Penerimaan
                        </label>

                        <input
                            type="date"
                            id="date"
                            name="date"
                            value="{{ $date }}"
                            max="{{ now()->format('Y-m-d') }}"
                            class="w-full rounded-lg border
                                border-slate-300 px-4 py-2.5 text-sm
                                outline-none transition
                                focus:border-blue-500 focus:ring-2
                                focus:ring-blue-200"
                        >

                        @error('date')
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
                                text-white transition hover:bg-slate-800"
                        >
                            Filter
                        </button>

                        <a
                            href="{{ route(
                                'goods-receipts.index'
                            ) }}"
                            class="rounded-lg border border-slate-300
                                px-4 py-2.5 text-center text-sm
                                font-semibold text-slate-600 transition
                                hover:bg-slate-50"
                        >
                            Reset
                        </a>
                    </div>
                </form>

                {{-- Keterangan filter aktif. --}}
                @if ($search !== '' || $date)
                    <div
                        class="mt-4 rounded-lg border border-blue-100
                            bg-blue-50 px-4 py-3 text-sm text-blue-700"
                    >
                        Menampilkan transaksi berdasarkan filter yang
                        dipilih.
                    </div>
                @endif
            </div>

            {{-- Tabel transaksi barang masuk. --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th
                                class="px-5 py-3 text-left text-xs
                                    font-semibold uppercase text-slate-500"
                            >
                                Tanggal
                            </th>

                            <th
                                class="px-5 py-3 text-left text-xs
                                    font-semibold uppercase text-slate-500"
                            >
                                Supplier
                            </th>

                            <th
                                class="px-5 py-3 text-center text-xs
                                    font-semibold uppercase text-slate-500"
                            >
                                Barang
                            </th>

                            <th
                                class="px-5 py-3 text-left text-xs
                                    font-semibold uppercase text-slate-500"
                            >
                                Petugas
                            </th>

                            <th
                                class="px-5 py-3 text-center text-xs
                                    font-semibold uppercase text-slate-500"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($receipts as $receipt)
                            <tr class="transition hover:bg-slate-50">

                                {{-- Tanggal penerimaan. --}}
                                <td
                                    class="whitespace-nowrap px-5 py-4
                                        text-sm font-semibold
                                        text-slate-800"
                                >
                                    {{ $receipt->received_at?->format(
                                        'd/m/Y'
                                    ) ?? '-' }}
                                </td>

                                {{-- Supplier. --}}
                                <td class="px-5 py-4 text-sm text-slate-700">
                                    {{ $receipt->supplier?->name ?? '-' }}
                                </td>

                                {{-- Ringkasan barang. --}}
                                <td
                                    class="whitespace-nowrap px-5 py-4
                                        text-center text-sm text-slate-600"
                                >
                                    <p
                                        class="font-semibold
                                            text-slate-800"
                                    >
                                        {{ number_format(
                                            (int) $receipt->details_count
                                        ) }}
                                        jenis
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ number_format(
                                            (int) (
                                                $receipt
                                                    ->details_sum_quantity
                                                ?? 0
                                            )
                                        ) }}
                                        unit masuk
                                    </p>
                                </td>

                                {{-- Petugas pencatat. --}}
                                <td class="px-5 py-4 text-sm text-slate-600">
                                    {{ $receipt->user?->name ?? '-' }}
                                </td>

                                {{-- Tombol detail. --}}
                                <td
                                    class="whitespace-nowrap px-5 py-4
                                        text-center"
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
                                    colspan="5"
                                    class="px-6 py-12 text-center"
                                >
                                    <i
                                        class="bi bi-box-arrow-in-down
                                            text-4xl text-slate-300"
                                    ></i>

                                    <p
                                        class="mt-3 font-medium
                                            text-slate-600"
                                    >
                                        Belum ada transaksi barang masuk.
                                    </p>

                                    <p
                                        class="mt-1 text-sm
                                            text-slate-400"
                                    >
                                        Tambahkan transaksi penerimaan
                                        barang dari supplier.
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
