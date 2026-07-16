@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        {{-- Judul halaman --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Barang Keluar
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola riwayat pengeluaran barang dari gudang.
                </p>
            </div>

            <a href="{{ route('goods-issues.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">

                <i class="bi bi-plus-lg"></i>

                Tambah Barang Keluar
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
                      action="{{ route('goods-issues.index') }}"
                      class="grid grid-cols-1 gap-3 md:grid-cols-4">

                    <div class="md:col-span-2">
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Cari nomor, tujuan, atau petugas..."
                               class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>

                    <input type="date"
                           name="date"
                           value="{{ $date }}"
                           class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">

                    <div class="flex gap-2">
                        <button type="submit"
                                class="flex-1 rounded-lg bg-slate-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Filter
                        </button>

                        <a href="{{ route('goods-issues.index') }}"
                           class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tabel transaksi --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Nomor
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Tanggal
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                Tujuan
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase text-slate-500">
                                Barang
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
                        @forelse ($issues as $issue)
                            <tr class="transition hover:bg-slate-50">
                                <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-800">
                                    {{ $issue->issue_number }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                    {{ $issue->issued_at->format('d/m/Y') }}
                                </td>

                                <td class="px-5 py-4 text-sm text-slate-700">
                                    {{ $issue->destination }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-center text-sm text-slate-600">
                                    <p class="font-semibold text-slate-800">
                                        {{ $issue->details_count }} jenis
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        {{ $issue->details_sum_quantity ?? 0 }}
                                        unit keluar
                                    </p>
                                </td>

                                <td class="px-5 py-4 text-sm text-slate-600">
                                    {{ $issue->user->name }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-center">
                                    <a href="{{ route('goods-issues.show', $issue) }}"
                                       title="Lihat detail"
                                       class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 text-blue-700 transition hover:bg-blue-200">

                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"
                                    class="px-6 py-12 text-center text-slate-500">

                                    <i class="bi bi-box-arrow-up text-4xl text-slate-300"></i>

                                    <p class="mt-3 font-medium text-slate-600">
                                        Belum ada transaksi barang keluar.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($issues->hasPages())
                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $issues->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
