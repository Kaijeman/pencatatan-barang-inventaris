@extends('layouts.app')

@section('title', 'Tambah Barang Keluar')

@section('content')
    @php
        /*
         * Mengambil kembali detail form ketika validasi gagal.
         */
        $formItems = old('items', [
            [
                'item_id' => '',
                'quantity' => 1,
            ],
        ]);
    @endphp

    <div class="mx-auto max-w-6xl space-y-6">
        {{-- Judul halaman. --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Tambah Barang Keluar
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Catat pengeluaran satu atau beberapa barang dari gudang.
            </p>
        </div>

        @if (! $hasAvailableItems)
            {{-- Peringatan stok tidak tersedia. --}}
            <div
                class="rounded-lg border border-amber-200
                    bg-amber-50 px-4 py-3 text-sm text-amber-700"
            >
                Tidak ada barang dengan stok tersedia. Tambahkan transaksi
                barang masuk terlebih dahulu.
            </div>
        @endif

        {{-- Kesalahan validasi umum. --}}
        @if ($errors->any())
            <div
                class="rounded-lg border border-red-200
                    bg-red-50 px-4 py-3 text-sm text-red-700"
            >
                Terdapat data yang belum benar. Periksa kembali formulir.
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('goods-issues.store') }}"
            class="space-y-6"
        >
            @csrf

            {{-- Informasi transaksi. --}}
            <div class="rounded-xl bg-white p-4 shadow-sm sm:p-6">
                <h2 class="mb-5 text-lg font-semibold text-slate-800">
                    Informasi Pengeluaran
                </h2>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    {{-- Tujuan pengeluaran. --}}
                    <div>
                        <label
                            for="destination"
                            class="mb-2 block text-sm font-semibold
                                text-slate-700"
                        >
                            Tujuan
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="destination"
                            name="destination"
                            value="{{ old('destination') }}"
                            placeholder="Contoh: Divisi Teknologi Informasi"
                            required
                            class="w-full rounded-lg border px-4
                                py-2.5 text-sm outline-none transition
                                @error('destination')
                                    border-red-500 focus:border-red-500
                                    focus:ring-2 focus:ring-red-200
                                @else
                                    border-slate-300
                                    focus:border-blue-500
                                    focus:ring-2 focus:ring-blue-200
                                @enderror"
                        >

                        @error('destination')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Tanggal pengeluaran. --}}
                    <div>
                        <label
                            for="issued_at"
                            class="mb-2 block text-sm font-semibold
                                text-slate-700"
                        >
                            Tanggal Pengeluaran
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="date"
                            id="issued_at"
                            name="issued_at"
                            value="{{ old(
                                'issued_at',
                                now()->format('Y-m-d')
                            ) }}"
                            max="{{ now()->format('Y-m-d') }}"
                            required
                            class="w-full rounded-lg border px-4
                                py-2.5 text-sm outline-none transition
                                @error('issued_at')
                                    border-red-500 focus:border-red-500
                                    focus:ring-2 focus:ring-red-200
                                @else
                                    border-slate-300
                                    focus:border-blue-500
                                    focus:ring-2 focus:ring-blue-200
                                @enderror"
                        >

                        @error('issued_at')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- Catatan transaksi. --}}
                <div class="mt-6">
                    <label
                        for="note"
                        class="mb-2 block text-sm font-semibold
                            text-slate-700"
                    >
                        Catatan
                    </label>

                    <textarea
                        id="note"
                        name="note"
                        rows="3"
                        placeholder="Masukkan keterangan pengeluaran barang..."
                        class="w-full rounded-lg border
                            border-slate-300 px-4 py-2.5 text-sm
                            outline-none transition
                            focus:border-blue-500 focus:ring-2
                            focus:ring-blue-200"
                    >{{ old('note') }}</textarea>

                    @error('note')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Detail barang keluar. --}}
            <div class="rounded-xl bg-white p-4 shadow-sm sm:p-6">
                <div
                    class="flex flex-col gap-4 sm:flex-row
                        sm:items-center sm:justify-between"
                >
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">
                            Detail Barang
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Jumlah pengeluaran tidak boleh melebihi stok.
                        </p>
                    </div>

                    {{-- Tombol tambah baris pada desktop. --}}
                    <button
                        type="button"
                        data-add-item-row
                        @disabled(! $hasAvailableItems)
                        class="hidden items-center justify-center
                            gap-2 rounded-lg bg-slate-700 px-4
                            py-2.5 text-sm font-semibold text-white
                            transition hover:bg-slate-800
                            disabled:cursor-not-allowed
                            disabled:opacity-50 sm:inline-flex"
                    >
                        <i class="bi bi-plus-lg"></i>

                        Tambah Baris
                    </button>
                </div>

                {{-- Header kolom desktop. --}}
                <div
                    class="mt-6 hidden gap-4 border-b
                        border-slate-200 px-3 pb-3 text-xs
                        font-semibold uppercase tracking-wide
                        text-slate-500 md:grid
                        md:grid-cols-[minmax(0,2fr)_8rem_9rem_3rem]"
                >
                    <div>Barang</div>
                    <div>Stok Tersedia</div>
                    <div>Jumlah Keluar</div>
                    <div class="text-center">Aksi</div>
                </div>

                {{-- Daftar detail barang. --}}
                <div
                    id="item-rows"
                    class="mt-4 space-y-4 md:space-y-0"
                >
                    @foreach ($formItems as $index => $formItem)
                        @php
                            /*
                             * Mencari barang yang sebelumnya dipilih.
                             */
                            $selectedItem = $items->firstWhere(
                                'id',
                                $formItem['item_id'] ?? null
                            );
                        @endphp

                        <div
                            class="item-row grid grid-cols-1 gap-4
                                rounded-xl border border-slate-200
                                bg-slate-50 p-4
                                md:grid-cols-[minmax(0,2fr)_8rem_9rem_3rem]
                                md:items-start md:rounded-none
                                md:border-x-0 md:border-t-0
                                md:bg-white md:px-3 md:py-4"
                        >
                            {{-- Pilihan barang. --}}
                            <div class="min-w-0">
                                <label
                                    class="mb-2 block text-xs
                                        font-semibold uppercase
                                        tracking-wide text-slate-500
                                        md:hidden"
                                >
                                    Barang
                                </label>

                                <select
                                    name="items[{{ $index }}][item_id]"
                                    required
                                    class="item-select w-full min-w-0
                                        rounded-lg border
                                        border-slate-300 bg-white
                                        px-3 py-2.5 text-sm
                                        text-slate-700 outline-none
                                        transition focus:border-blue-500
                                        focus:ring-2 focus:ring-blue-200"
                                >
                                    <option value="">
                                        Pilih barang
                                    </option>

                                    @foreach ($items as $item)
                                        <option
                                            value="{{ $item->id }}"
                                            data-stock="{{ $item->stock }}"
                                            data-unit="{{ $item->unit }}"
                                            @disabled(
                                                (int) $item->stock <= 0
                                            )
                                            @selected(
                                                (
                                                    $formItem[
                                                        'item_id'
                                                    ] ?? ''
                                                ) == $item->id
                                            )
                                        >
                                            {{ $item->name }}
                                            — {{ $item->unit }}
                                            — {{ $item->category?->name
                                                ?? '-' }}

                                            @if ((int) $item->stock <= 0)
                                                — stok habis
                                            @endif
                                        </option>
                                    @endforeach
                                </select>

                                @error("items.$index.item_id")
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Stok tersedia. --}}
                            <div>
                                <span
                                    class="mb-2 block text-xs
                                        font-semibold uppercase
                                        tracking-wide text-slate-500
                                        md:hidden"
                                >
                                    Stok Tersedia
                                </span>

                                <div
                                    class="stock-display flex min-h-10
                                        items-center rounded-lg border
                                        border-slate-200 bg-white px-3
                                        py-2.5 text-sm font-semibold
                                        text-slate-700
                                        md:border-transparent
                                        md:bg-transparent md:px-0"
                                >
                                    @if ($selectedItem)
                                        {{ number_format(
                                            (int) $selectedItem->stock
                                        ) }}
                                        {{ $selectedItem->unit }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            {{-- Jumlah barang keluar. --}}
                            <div>
                                <label
                                    class="mb-2 block text-xs
                                        font-semibold uppercase
                                        tracking-wide text-slate-500
                                        md:hidden"
                                >
                                    Jumlah Keluar
                                </label>

                                <input
                                    type="number"
                                    name="items[{{ $index }}][quantity]"
                                    value="{{ $formItem[
                                        'quantity'
                                    ] ?? 1 }}"
                                    min="1"
                                    step="1"
                                    @if ($selectedItem)
                                        max="{{ $selectedItem->stock }}"
                                    @endif
                                    required
                                    class="quantity-input w-full
                                        rounded-lg border
                                        border-slate-300 bg-white
                                        px-3 py-2.5 text-sm
                                        text-slate-700 outline-none
                                        transition focus:border-blue-500
                                        focus:ring-2 focus:ring-blue-200"
                                >

                                @error("items.$index.quantity")
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Tombol hapus baris. --}}
                            <div>
                                <span
                                    class="mb-2 block text-xs
                                        font-semibold uppercase
                                        tracking-wide text-slate-500
                                        md:hidden"
                                >
                                    Aksi
                                </span>

                                <button
                                    type="button"
                                    title="Hapus baris"
                                    class="remove-item-row
                                        inline-flex h-10 w-full
                                        items-center justify-center
                                        gap-2 rounded-lg bg-red-100
                                        px-3 text-sm font-semibold
                                        text-red-700 transition
                                        hover:bg-red-200 md:w-10
                                        md:px-0"
                                >
                                    <i class="bi bi-trash"></i>

                                    <span class="md:hidden">
                                        Hapus Baris
                                    </span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Tombol tambah baris pada mobile. --}}
                <button
                    type="button"
                    data-add-item-row
                    @disabled(! $hasAvailableItems)
                    class="mt-4 inline-flex w-full items-center
                        justify-center gap-2 rounded-lg
                        bg-slate-700 px-4 py-3 text-sm
                        font-semibold text-white transition
                        hover:bg-slate-800
                        disabled:cursor-not-allowed
                        disabled:opacity-50 sm:hidden"
                >
                    <i class="bi bi-plus-lg"></i>

                    Tambah Baris
                </button>

                @error('items')
                    <p class="mt-3 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Tombol form. --}}
            <div
                class="flex flex-col-reverse gap-3
                    sm:flex-row sm:justify-end"
            >
                <a
                    href="{{ route('goods-issues.index') }}"
                    class="rounded-lg border border-slate-300
                        bg-white px-5 py-2.5 text-center
                        text-sm font-semibold text-slate-600
                        transition hover:bg-slate-50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    @disabled(! $hasAvailableItems)
                    class="inline-flex items-center justify-center
                        gap-2 rounded-lg bg-blue-600 px-5
                        py-2.5 text-sm font-semibold text-white
                        transition hover:bg-blue-700
                        disabled:cursor-not-allowed
                        disabled:opacity-50"
                >
                    <i class="bi bi-save"></i>

                    Simpan Barang Keluar
                </button>
            </div>
        </form>
    </div>

    {{-- Template baris barang baru. --}}
    <template id="item-row-template">
        <div
            class="item-row grid grid-cols-1 gap-4
                rounded-xl border border-slate-200
                bg-slate-50 p-4
                md:grid-cols-[minmax(0,2fr)_8rem_9rem_3rem]
                md:items-start md:rounded-none
                md:border-x-0 md:border-t-0
                md:bg-white md:px-3 md:py-4"
        >
            {{-- Pilihan barang. --}}
            <div class="min-w-0">
                <label
                    class="mb-2 block text-xs font-semibold
                        uppercase tracking-wide text-slate-500
                        md:hidden"
                >
                    Barang
                </label>

                <select
                    name="items[__INDEX__][item_id]"
                    required
                    class="item-select w-full min-w-0
                        rounded-lg border border-slate-300
                        bg-white px-3 py-2.5 text-sm
                        text-slate-700 outline-none transition
                        focus:border-blue-500 focus:ring-2
                        focus:ring-blue-200"
                >
                    <option value="">
                        Pilih barang
                    </option>

                    @foreach ($items as $item)
                        <option
                            value="{{ $item->id }}"
                            data-stock="{{ $item->stock }}"
                            data-unit="{{ $item->unit }}"
                            @disabled((int) $item->stock <= 0)
                        >
                            {{ $item->name }}
                            — {{ $item->unit }}
                            — {{ $item->category?->name ?? '-' }}

                            @if ((int) $item->stock <= 0)
                                — stok habis
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Stok tersedia. --}}
            <div>
                <span
                    class="mb-2 block text-xs font-semibold
                        uppercase tracking-wide text-slate-500
                        md:hidden"
                >
                    Stok Tersedia
                </span>

                <div
                    class="stock-display flex min-h-10
                        items-center rounded-lg border
                        border-slate-200 bg-white px-3
                        py-2.5 text-sm font-semibold
                        text-slate-700 md:border-transparent
                        md:bg-transparent md:px-0"
                >
                    -
                </div>
            </div>

            {{-- Jumlah barang keluar. --}}
            <div>
                <label
                    class="mb-2 block text-xs font-semibold
                        uppercase tracking-wide text-slate-500
                        md:hidden"
                >
                    Jumlah Keluar
                </label>

                <input
                    type="number"
                    name="items[__INDEX__][quantity]"
                    value="1"
                    min="1"
                    step="1"
                    required
                    class="quantity-input w-full rounded-lg
                        border border-slate-300 bg-white
                        px-3 py-2.5 text-sm text-slate-700
                        outline-none transition
                        focus:border-blue-500 focus:ring-2
                        focus:ring-blue-200"
                >
            </div>

            {{-- Tombol hapus baris. --}}
            <div>
                <span
                    class="mb-2 block text-xs font-semibold
                        uppercase tracking-wide text-slate-500
                        md:hidden"
                >
                    Aksi
                </span>

                <button
                    type="button"
                    title="Hapus baris"
                    class="remove-item-row inline-flex h-10
                        w-full items-center justify-center gap-2
                        rounded-lg bg-red-100 px-3 text-sm
                        font-semibold text-red-700 transition
                        hover:bg-red-200 md:w-10 md:px-0"
                >
                    <i class="bi bi-trash"></i>

                    <span class="md:hidden">
                        Hapus Baris
                    </span>
                </button>
            </div>
        </div>
    </template>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rowsContainer =
                document.getElementById('item-rows');

            const addButtons =
                document.querySelectorAll(
                    '[data-add-item-row]'
                );

            const rowTemplate =
                document.getElementById('item-row-template');

            if (
                ! rowsContainer
                || addButtons.length === 0
                || ! rowTemplate
            ) {
                return;
            }

            let rowIndex = rowsContainer
                .querySelectorAll('.item-row')
                .length;

            /**
             * Memperbarui stok berdasarkan barang terpilih.
             */
            function handleItemChange(event) {
                const select = event.currentTarget;
                const row = select.closest('.item-row');

                if (! row) {
                    return;
                }

                const stockDisplay =
                    row.querySelector('.stock-display');

                const quantityInput =
                    row.querySelector('.quantity-input');

                const selectedOption = select.options[
                    select.selectedIndex
                ];

                if (
                    ! stockDisplay
                    || ! quantityInput
                ) {
                    return;
                }

                const stock =
                    selectedOption?.dataset.stock ?? '';

                const unit =
                    selectedOption?.dataset.unit ?? '';

                if (stock !== '') {
                    stockDisplay.textContent =
                        `${Number(stock).toLocaleString(
                            'id-ID'
                        )} ${unit}`;

                    quantityInput.max = stock;

                    if (
                        Number(quantityInput.value)
                        > Number(stock)
                    ) {
                        quantityInput.value = stock;
                    }

                    return;
                }

                stockDisplay.textContent = '-';
                quantityInput.removeAttribute('max');

                if (Number(quantityInput.value) < 1) {
                    quantityInput.value = 1;
                }
            }

            /**
             * Menghapus baris barang dari formulir.
             */
            function handleRemoveRow(event) {
                const rows = rowsContainer.querySelectorAll(
                    '.item-row'
                );

                if (rows.length <= 1) {
                    window.appAlert(
                        'Minimal satu barang harus tersedia dalam transaksi.',
                        {
                            title: 'Baris Tidak Dapat Dihapus',
                            type: 'warning',
                            confirmText: 'Mengerti',
                        }
                    );

                    return;
                }

                event.currentTarget
                    .closest('.item-row')
                    ?.remove();
            }

            /**
             * Memasang event pada satu baris barang.
             */
            function attachRowEvents(row) {
                const itemSelect =
                    row.querySelector('.item-select');

                const removeButton =
                    row.querySelector('.remove-item-row');

                itemSelect?.addEventListener(
                    'change',
                    handleItemChange
                );

                removeButton?.addEventListener(
                    'click',
                    handleRemoveRow
                );
            }

            /**
             * Menambahkan baris barang baru.
             */
            function addItemRow() {
                const html =
                    rowTemplate.innerHTML.replaceAll(
                        '__INDEX__',
                        rowIndex
                    );

                rowsContainer.insertAdjacentHTML(
                    'beforeend',
                    html
                );

                const newRow =
                    rowsContainer.lastElementChild;

                if (newRow) {
                    attachRowEvents(newRow);

                    if (window.innerWidth < 640) {
                        newRow.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center',
                        });
                    }
                }

                rowIndex++;
            }

            /**
             * Memasang event pada tombol tambah desktop dan mobile.
             */
            addButtons.forEach((button) => {
                button.addEventListener(
                    'click',
                    addItemRow
                );
            });

            /**
             * Memasang event pada baris yang sudah tersedia.
             */
            rowsContainer
                .querySelectorAll('.item-row')
                .forEach(attachRowEvents);
        });
    </script>
@endpush
