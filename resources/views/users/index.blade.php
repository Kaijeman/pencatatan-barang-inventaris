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
                    Manajemen Pengguna
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Lihat pengguna, tambahkan akun baru,
                    dan kelola akun Anda sendiri.
                </p>
            </div>

            {{-- Tombol tambah pengguna. --}}
            <a
                href="{{ route('users.create') }}"
                class="inline-flex items-center justify-center gap-2
                    rounded-lg bg-blue-600 px-4 py-2.5 text-sm
                    font-semibold text-white transition
                    hover:bg-blue-700"
            >
                <i class="bi bi-person-plus"></i>

                Tambah Pengguna
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

            {{-- Form pencarian pengguna. --}}
            <div class="border-b border-slate-200 p-5">
                <form
                    method="GET"
                    action="{{ route('users.index') }}"
                    class="grid grid-cols-1 gap-4
                        lg:grid-cols-4 lg:items-end"
                >
                    <div class="lg:col-span-3">
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
                            placeholder="Cari nama atau email pengguna..."
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

                    <div class="flex gap-2">
                        <button
                            type="submit"
                            class="flex-1 rounded-lg bg-slate-700
                                px-4 py-2.5 text-sm font-semibold
                                text-white transition hover:bg-slate-800"
                        >
                            Cari
                        </button>

                        <a
                            href="{{ route('users.index') }}"
                            class="rounded-lg border border-slate-300
                                px-4 py-2.5 text-center text-sm
                                font-semibold text-slate-600 transition
                                hover:bg-slate-50"
                        >
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tabel pengguna. --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th
                                class="w-20 px-5 py-3 text-left text-xs
                                    font-semibold uppercase text-slate-500"
                            >
                                No.
                            </th>

                            <th
                                class="px-5 py-3 text-left text-xs
                                    font-semibold uppercase text-slate-500"
                            >
                                Pengguna
                            </th>

                            <th
                                class="px-5 py-3 text-left text-xs
                                    font-semibold uppercase text-slate-500"
                            >
                                Email
                            </th>

                            <th
                                class="px-5 py-3 text-left text-xs
                                    font-semibold uppercase text-slate-500"
                            >
                                Dibuat
                            </th>

                            <th
                                class="w-40 px-5 py-3 text-center text-xs
                                    font-semibold uppercase text-slate-500"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($users as $user)
                            <tr class="transition hover:bg-slate-50">

                                {{-- Nomor urut. --}}
                                <td
                                    class="whitespace-nowrap px-5 py-4
                                        text-sm text-slate-500"
                                >
                                    {{ ($users->currentPage() - 1)
                                        * $users->perPage()
                                        + $loop->iteration }}
                                </td>

                                {{-- Informasi pengguna. --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-10 w-10
                                                items-center justify-center
                                                rounded-full bg-blue-100
                                                text-sm font-bold
                                                text-blue-700"
                                        >
                                            {{ strtoupper(
                                                mb_substr(
                                                    $user->name,
                                                    0,
                                                    1
                                                )
                                            ) }}
                                        </div>

                                        <div>
                                            <p
                                                class="font-semibold
                                                    text-slate-800"
                                            >
                                                {{ $user->name }}
                                            </p>

                                            @if (
                                                $user->id === auth()->id()
                                            )
                                                <p
                                                    class="text-xs
                                                        font-medium
                                                        text-blue-600"
                                                >
                                                    Akun Anda
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Email pengguna. --}}
                                <td
                                    class="px-5 py-4 text-sm
                                        text-slate-600"
                                >
                                    {{ $user->email }}
                                </td>

                                {{-- Tanggal dibuat. --}}
                                <td
                                    class="whitespace-nowrap px-5 py-4
                                        text-sm text-slate-600"
                                >
                                    {{ $user->created_at?->format(
                                        'd/m/Y'
                                    ) ?? '-' }}
                                </td>

                                {{-- Aksi pengguna. --}}
                                <td
                                    class="whitespace-nowrap px-5 py-4
                                        text-center"
                                >
                                    @if ($user->id === auth()->id())
                                        <a
                                            href="{{ route(
                                                'users.edit',
                                                $user
                                            ) }}"
                                            title="Edit akun saya"
                                            class="inline-flex h-9 w-9
                                                items-center justify-center
                                                rounded-lg bg-amber-100
                                                text-amber-700 transition
                                                hover:bg-amber-200"
                                        >
                                            <i
                                                class="bi bi-pencil-square"
                                            ></i>
                                        </a>
                                    @else
                                        <span
                                            class="text-xs
                                                text-slate-400"
                                        >
                                            Tidak tersedia
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="5"
                                    class="px-6 py-12 text-center"
                                >
                                    <i
                                        class="bi bi-people text-4xl
                                            text-slate-300"
                                    ></i>

                                    <p
                                        class="mt-3 font-medium
                                            text-slate-600"
                                    >
                                        Tidak ada pengguna yang sesuai.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination. --}}
            @if ($users->hasPages())
                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
