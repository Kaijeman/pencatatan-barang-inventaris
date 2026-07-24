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
                class="rounded-lg border border-amber-200 bg-amber-50
                    px-4 py-3 text-sm text-amber-700"
            >
                Tidak ada barang dengan stok tersedia. Tambahkan transaksi
                barang masuk terlebih dahulu.
            </div>
        @endif

        {{-- Kesalahan validasi umum. --}}
        @if ($errors->any())
            <div
                class="rounded-lg border border-red-200 bg-red-50
                    px-4 py-3 text-sm text-red-700"
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
            <div class="rounded-xl bg-white p-6 shadow-sm">
                <h2 class="mb-5 text-lg font-semibold text-slate-800">
                    Informasi Pengeluaran
                </h2>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
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
                            class="w-full rounded-lg border px-4 py-2.5
                                text-sm outline-none transition
                                @error('destination')
                                    border-red-500
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
                            class="w-full rounded-lg border px-4 py-2.5
                                text-sm outline-none transition
                                @error('issued_at')
                                    border-red-500
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
                        class="w-full rounded-lg border border-slate-300
                            px-4 py-2.5 text-sm outline-none transition
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

            {{-- Detail barang. --}}
            <div class="rounded-xl bg-white p-6 shadow-sm">
                <div
                    class="mb-5 flex flex-col gap-3 sm:flex-row
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

                    <button
                        type="button"
                        id="add-item-row"
                        class="inline-flex items-center justify-center
                            gap-2 rounded-lg bg-slate-700 px-4 py-2.5
                            text-sm font-semibold text-white transition
                            hover:bg-slate-800"
                    >
                        <i class="bi bi-plus-lg"></i>

                        Tambah Baris
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th
                                    class="px-3 py-3 text-left text-xs
                                        font-semibold uppercase
                                        text-slate-500"
                                >
                                    Barang
                                </th>

                                <th
                                    class="w-48 px-3 py-3 text-left
                                        text-xs font-semibold uppercase
                                        text-slate-500"
                                >
                                    Stok Tersedia
                                </th>

                                <th
                                    class="w-48 px-3 py-3 text-left
                                        text-xs font-semibold uppercase
                                        text-slate-500"
                                >
                                    Jumlah Keluar
                                </th>

                                <th
                                    class="w-20 px-3 py-3 text-center
                                        text-xs font-semibold uppercase
                                        text-slate-500"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody id="item-rows">
                            @foreach ($formItems as $index => $formItem)
                                @php
                                    /*
                                     * Mencari data barang yang sebelumnya
                                     * dipilih.
                                     */
                                    $selectedItem = $items->firstWhere(
                                        'id',
                                        $formItem['item_id'] ?? null
                                    );
                                @endphp

                                <tr
                                    class="item-row border-b
                                        border-slate-100"
                                >
                                    <td class="px-3 py-4">
                                        <select
                                            name="items[{{ $index }}][item_id]"
                                            class="item-select w-full
                                                rounded-lg border
                                                border-slate-300 px-3
                                                py-2.5 text-sm"
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
                                                        (int) $item->stock
                                                        <= 0
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
                                                    -
                                                    {{ $item->unit }}
                                                    (
                                                    {{ $item->category?->name
                                                        ?? '-' }}
                                                    )

                                                    @if (
                                                        (int) $item->stock
                                                        <= 0
                                                    )
                                                        - stok habis
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>

                                        @error("items.$index.item_id")
                                            <p
                                                class="mt-2 text-sm
                                                    text-red-600"
                                            >
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </td>

                                    <td class="px-3 py-4">
                                        <span
                                            class="stock-display text-sm
                                                font-semibold
                                                text-slate-700"
                                        >
                                            @if ($selectedItem)
                                                {{ number_format(
                                                    (int) $selectedItem
                                                        ->stock
                                                ) }}
                                                {{ $selectedItem->unit }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </td>

                                    <td class="px-3 py-4">
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
                                            class="quantity-input w-full
                                                rounded-lg border
                                                border-slate-300 px-3
                                                py-2.5 text-sm"
                                        >

                                        @error("items.$index.quantity")
                                            <p
                                                class="mt-2 text-sm
                                                    text-red-600"
                                            >
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </td>

                                    <td class="px-3 py-4 text-center">
                                        <button
                                            type="button"
                                            class="remove-item-row
                                                inline-flex h-9 w-9
                                                items-center
                                                justify-center rounded-lg
                                                bg-red-100 text-red-700
                                                transition
                                                hover:bg-red-200"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @error('items')
                    <p class="mt-3 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Tombol form. --}}
            <div
                class="flex flex-col-reverse gap-3 sm:flex-row
                    sm:justify-end"
            >
                <a
                    href="{{ route('goods-issues.index') }}"
                    class="rounded-lg border border-slate-300 bg-white
                        px-5 py-2.5 text-center text-sm font-semibold
                        text-slate-600 transition hover:bg-slate-50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    @disabled(! $hasAvailableItems)
                    class="inline-flex items-center justify-center
                        gap-2 rounded-lg bg-blue-600 px-5 py-2.5
                        text-sm font-semibold text-white transition
                        hover:bg-blue-700 disabled:cursor-not-allowed
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
        <tr class="item-row border-b border-slate-100">
            <td class="px-3 py-4">
                <select
                    name="items[__INDEX__][item_id]"
                    class="item-select w-full rounded-lg border
                        border-slate-300 px-3 py-2.5 text-sm"
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
                            -
                            {{ $item->unit }}
                            (
                            {{ $item->category?->name ?? '-' }}
                            )

                            @if ((int) $item->stock <= 0)
                                - stok habis
                            @endif
                        </option>
                    @endforeach
                </select>
            </td>

            <td class="px-3 py-4">
                <span
                    class="stock-display text-sm font-semibold
                        text-slate-700"
                >
                    -
                </span>
            </td>

            <td class="px-3 py-4">
                <input
                    type="number"
                    name="items[__INDEX__][quantity]"
                    value="1"
                    min="1"
                    step="1"
                    class="quantity-input w-full rounded-lg border
                        border-slate-300 px-3 py-2.5 text-sm"
                >
            </td>

            <td class="px-3 py-4 text-center">
                <button
                    type="button"
                    class="remove-item-row inline-flex h-9 w-9
                        items-center justify-center rounded-lg
                        bg-red-100 text-red-700 transition
                        hover:bg-red-200"
                >
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    </template>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rowsContainer =
                document.getElementById('item-rows');

            const addButton =
                document.getElementById('add-item-row');

            const rowTemplate = document.getElementById(
                'item-row-template'
            );

            let rowIndex = rowsContainer
                .querySelectorAll('.item-row')
                .length;

            /**
             * Memperbarui informasi stok berdasarkan barang terpilih.
             */
            function handleItemChange(event) {
                const select = event.currentTarget;
                const row = select.closest('.item-row');

                const stockDisplay =
                    row.querySelector('.stock-display');

                const quantityInput =
                    row.querySelector('.quantity-input');

                const selectedOption = select.options[
                    select.selectedIndex
                ];

                const stock =
                    selectedOption?.dataset.stock ?? '';

                const unit =
                    selectedOption?.dataset.unit ?? '';

                if (stock !== '') {
                    stockDisplay.textContent =
                        `${stock} ${unit}`;

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
                }

                event.currentTarget
                    .closest('.item-row')
                    .remove();
            }

            /**
             * Memasang event pada satu baris barang.
             */
            function attachRowEvents(row) {
                row.querySelector('.item-select')
                    .addEventListener(
                        'change',
                        handleItemChange
                    );

                row.querySelector('.remove-item-row')
                    .addEventListener(
                        'click',
                        handleRemoveRow
                    );
            }

            /**
             * Menambahkan baris barang baru.
             */
            addButton.addEventListener('click', () => {
                const html = rowTemplate.innerHTML.replaceAll(
                    '__INDEX__',
                    rowIndex
                );

                rowsContainer.insertAdjacentHTML(
                    'beforeend',
                    html
                );

                const newRow =
                    rowsContainer.lastElementChild;

                attachRowEvents(newRow);

                rowIndex++;
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
