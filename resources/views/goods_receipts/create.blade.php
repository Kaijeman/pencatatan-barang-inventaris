@extends('layouts.app')

@section('content')
    @php
        /*
         * Mengambil kembali detail form ketika validasi gagal.
         */
        $formItems = old('items', [
            [
                'item_id' => '',
                'quantity' => 1,
                'purchase_price' => '',
            ],
        ]);
    @endphp

    <div class="mx-auto max-w-6xl space-y-6">

        {{-- Judul halaman. --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Tambah Barang Masuk
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Catat penerimaan satu atau beberapa barang dari supplier.
            </p>
        </div>

        @if ($suppliers->isEmpty() || $items->isEmpty())
            {{-- Peringatan data master belum tersedia. --}}
            <div
                class="rounded-lg border border-amber-200 bg-amber-50
                    px-4 py-3 text-sm text-amber-700"
            >
                Data supplier dan barang harus tersedia sebelum membuat
                transaksi barang masuk.
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
            action="{{ route('goods-receipts.store') }}"
            class="space-y-6"
        >
            @csrf

            {{-- Informasi transaksi. --}}
            <div class="rounded-xl bg-white p-6 shadow-sm">
                <h2 class="mb-5 text-lg font-semibold text-slate-800">
                    Informasi Penerimaan
                </h2>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label
                            for="supplier_id"
                            class="mb-2 block text-sm font-semibold
                                text-slate-700"
                        >
                            Supplier
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="supplier_id"
                            name="supplier_id"
                            class="w-full rounded-lg border px-4 py-2.5
                                text-sm outline-none transition
                                @error('supplier_id')
                                    border-red-500
                                @else
                                    border-slate-300
                                    focus:border-blue-500
                                    focus:ring-2 focus:ring-blue-200
                                @enderror"
                        >
                            <option value="">
                                Pilih supplier
                            </option>

                            @foreach ($suppliers as $supplier)
                                <option
                                    value="{{ $supplier->id }}"
                                    @selected(
                                        old('supplier_id')
                                        == $supplier->id
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

                    <div>
                        <label
                            for="received_at"
                            class="mb-2 block text-sm font-semibold
                                text-slate-700"
                        >
                            Tanggal Penerimaan
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="date"
                            id="received_at"
                            name="received_at"
                            value="{{ old(
                                'received_at',
                                now()->format('Y-m-d')
                            ) }}"
                            max="{{ now()->format('Y-m-d') }}"
                            class="w-full rounded-lg border px-4 py-2.5
                                text-sm outline-none transition
                                @error('received_at')
                                    border-red-500
                                @else
                                    border-slate-300
                                    focus:border-blue-500
                                    focus:ring-2 focus:ring-blue-200
                                @enderror"
                        >

                        @error('received_at')
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
                        placeholder="Masukkan nomor surat jalan atau catatan lain..."
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
                            Tambahkan seluruh barang dalam satu penerimaan.
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
                                    class="w-40 px-3 py-3 text-left
                                        text-xs font-semibold uppercase
                                        text-slate-500"
                                >
                                    Jumlah
                                </th>

                                <th
                                    class="w-64 px-3 py-3 text-left
                                        text-xs font-semibold uppercase
                                        text-slate-500"
                                >
                                    Harga Beli
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
                                                    data-price="{{ $item->purchase_price }}"
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
                                                    (stok:
                                                    {{ number_format(
                                                        (int) $item->stock
                                                    ) }}
                                                    {{ $item->unit }})
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
                                        <input
                                            type="number"
                                            name="items[{{ $index }}][quantity]"
                                            value="{{ $formItem[
                                                'quantity'
                                            ] ?? 1 }}"
                                            min="1"
                                            step="1"
                                            class="w-full rounded-lg
                                                border border-slate-300
                                                px-3 py-2.5 text-sm"
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

                                    <td class="px-3 py-4">
                                        <input
                                            type="number"
                                            name="items[{{ $index }}][purchase_price]"
                                            value="{{ $formItem[
                                                'purchase_price'
                                            ] ?? '' }}"
                                            min="0"
                                            step="0.01"
                                            class="price-input w-full
                                                rounded-lg border
                                                border-slate-300 px-3
                                                py-2.5 text-sm"
                                        >

                                        @error(
                                            "items.$index.purchase_price"
                                        )
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
                    href="{{ route('goods-receipts.index') }}"
                    class="rounded-lg border border-slate-300 bg-white
                        px-5 py-2.5 text-center text-sm font-semibold
                        text-slate-600 transition hover:bg-slate-50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    @disabled(
                        $suppliers->isEmpty()
                        || $items->isEmpty()
                    )
                    class="inline-flex items-center justify-center
                        gap-2 rounded-lg bg-blue-600 px-5 py-2.5
                        text-sm font-semibold text-white transition
                        hover:bg-blue-700 disabled:cursor-not-allowed
                        disabled:opacity-50"
                >
                    <i class="bi bi-save"></i>

                    Simpan Barang Masuk
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
                            data-price="{{ $item->purchase_price }}"
                        >
                            {{ $item->name }}
                            -
                            {{ $item->unit }}
                            (stok:
                            {{ number_format((int) $item->stock) }}
                            {{ $item->unit }})
                        </option>
                    @endforeach
                </select>
            </td>

            <td class="px-3 py-4">
                <input
                    type="number"
                    name="items[__INDEX__][quantity]"
                    value="1"
                    min="1"
                    step="1"
                    class="w-full rounded-lg border border-slate-300
                        px-3 py-2.5 text-sm"
                >
            </td>

            <td class="px-3 py-4">
                <input
                    type="number"
                    name="items[__INDEX__][purchase_price]"
                    min="0"
                    step="0.01"
                    class="price-input w-full rounded-lg border
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

            const rowTemplate =
                document.getElementById('item-row-template');

            let rowIndex = rowsContainer
                .querySelectorAll('.item-row')
                .length;

            /**
             * Mengisi harga beli berdasarkan barang yang dipilih.
             */
            function handleItemChange(event) {
                const select = event.currentTarget;
                const row = select.closest('.item-row');
                const priceInput =
                    row.querySelector('.price-input');

                const selectedOption = select.options[
                    select.selectedIndex
                ];

                if (
                    selectedOption
                    && selectedOption.dataset.price
                ) {
                    priceInput.value =
                        selectedOption.dataset.price;

                    return;
                }

                priceInput.value = '';
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
