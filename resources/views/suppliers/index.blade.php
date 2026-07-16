@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Data Supplier
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola informasi pemasok barang gudang.
                </p>
            </div>

            <a href="{{ route('suppliers.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">

                <i class="bi bi-plus-lg"></i>

                Tambah Supplier
            </a>
        </div>

        @if (session('success'))
            <div class="flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                <i class="bi bi-check-circle-fill mt-0.5"></i>

                <span class="text-sm">
                    {{ session('success') }}
                </span>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                <i class="bi bi-exclamation-circle-fill mt-0.5"></i>

                <span class="text-sm">
                    {{ session('error') }}
                </span>
            </div>
        @endif

        <div class="overflow-hidden rounded-xl bg-white shadow-sm">

            <div class="border-b border-slate-200 p-5">
                <form method="GET"
                      action="{{ route('suppliers.index') }}"
                      class="flex flex-col gap-3 sm:flex-row">

                    <div class="relative flex-1">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>

                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Cari nama, telepon, email, atau alamat..."
                               class="w-full rounded-lg border border-slate-300 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>

                    <button type="submit"
                            class="rounded-lg bg-slate-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Cari
                    </button>

                    @if ($search !== '')
                        <a href="{{ route('suppliers.index') }}"
                           class="rounded-lg border border-slate-300 px-5 py-2.5 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">

                    <thead class="bg-slate-50">
                        <tr>
                            <th class="w-20 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                No.
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Nama Supplier
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Kontak
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Alamat
                            </th>

                            <th class="w-40 px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($suppliers as $supplier)
                            <tr class="transition hover:bg-slate-50">

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500">
                                    {{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $loop->iteration }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="font-semibold text-slate-800">
                                        {{ $supplier->name }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    <div class="space-y-1">
                                        <div>
                                            <i class="bi bi-telephone mr-1 text-slate-400"></i>
                                            {{ $supplier->phone ?: '-' }}
                                        </div>

                                        <div>
                                            <i class="bi bi-envelope mr-1 text-slate-400"></i>
                                            {{ $supplier->email ?: '-' }}
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $supplier->address
                                        ? \Illuminate\Support\Str::limit($supplier->address, 60)
                                        : '-' }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">

                                        <a href="{{ route('suppliers.edit', $supplier) }}"
                                           title="Edit supplier"
                                           class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-700 transition hover:bg-amber-200">

                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form method="POST"
                                              action="{{ route('suppliers.destroy', $supplier) }}"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    title="Hapus supplier"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-100 text-red-700 transition hover:bg-red-200">

                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="px-6 py-12 text-center">

                                    <i class="bi bi-truck text-4xl text-slate-300"></i>

                                    <p class="mt-3 font-medium text-slate-600">
                                        Belum ada data supplier.
                                    </p>

                                    <p class="mt-1 text-sm text-slate-400">
                                        Tambahkan supplier untuk mulai mencatat pemasok barang.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            @if ($suppliers->hasPages())
                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $suppliers->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection
