@extends('layouts.app')

@section('title', 'Tambah Barang Masuk')

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
                class="rounded-lg border border-amber-200
                    bg-amber-50 px-4 py-3 text-sm text-amber-700"
            >
                Data supplier dan barang harus tersedia sebelum membuat
                transaksi barang masuk.
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
            action="{{ route('goods-receipts.store') }}"
            class="space-y-6"
        >
            @csrf

            {{-- Informasi transaksi. --}}
            <div class="rounded-xl bg-white p-4 shadow-sm sm:p-6">
                <h2 class="mb-5 text-lg font-semibold text-slate-800">
                    Informasi Penerimaan
                </h2>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    {{-- Supplier. --}}
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
                            required
                            class="w-full rounded-lg border px-4
                                py-2.5 text-sm outline-none transition
                                @error('supplier_id')
                                    border-red-500 focus:border-red-500
                                    focus:ring-2 focus:ring-red-200
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

                    {{-- Tanggal penerimaan. --}}
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
                            required
                            class="w-full rounded-lg border px-4
                                py-2.5 text-sm outline-none transition
                                @error('received_at')
                                    border-red-500 focus:border-red-500
                                    focus:ring-2 focus:ring-red-200
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
                        placeholder="Masukkan keterangan masuknya barang..."
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

            {{-- Detail barang masuk. --}}
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
                            Tambahkan seluruh barang dalam satu penerimaan.
                        </p>
                    </div>

                    {{-- Tombol tambah baris pada desktop. --}}
                    <button
                        type="button"
                        data-add-item-row
                        class="hidden items-center justify-center
                            gap-2 rounded-lg bg-slate-700 px-4
                            py-2.5 text-sm font-semibold text-white
                            transition hover:bg-slate-800 sm:inline-flex"
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
                        md:grid-cols-[minmax(0,2fr)_8rem_12rem_3rem]"
                >
                    <div>Barang</div>
                    <div>Jumlah</div>
                    <div>Harga Beli</div>
                    <div class="text-center">Aksi</div>
                </div>

                {{-- Daftar detail barang. --}}
                <div
                    id="item-rows"
                    class="mt-4 space-y-4 md:space-y-0"
                >
                    @foreach ($formItems as $index => $formItem)
                        <div
                            class="item-row grid grid-cols-1 gap-4
                                rounded-xl border border-slate-200
                                bg-slate-50 p-4
                                md:grid-cols-[minmax(0,2fr)_8rem_12rem_3rem]
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
                                            — {{ $item->unit }}
                                            — stok
                                            {{ number_format(
                                                (int) $item->stock
                                            ) }}
                                            {{ $item->unit }}
                                        </option>
                                    @endforeach
                                </select>

                                @error("items.$index.item_id")
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Jumlah barang. --}}
                            <div>
                                <label
                                    class="mb-2 block text-xs
                                        font-semibold uppercase
                                        tracking-wide text-slate-500
                                        md:hidden"
                                >
                                    Jumlah
                                </label>

                                <input
                                    type="number"
                                    name="items[{{ $index }}][quantity]"
                                    value="{{ $formItem[
                                        'quantity'
                                    ] ?? 1 }}"
                                    min="1"
                                    step="1"
                                    required
                                    class="w-full rounded-lg border
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

                            {{-- Harga beli. --}}
                            <div>
                                <label
                                    class="mb-2 block text-xs
                                        font-semibold uppercase
                                        tracking-wide text-slate-500
                                        md:hidden"
                                >
                                    Harga Beli
                                </label>

                                <input
                                    type="number"
                                    name="items[{{ $index }}][purchase_price]"
                                    value="{{ $formItem[
                                        'purchase_price'
                                    ] ?? '' }}"
                                    min="0"
                                    step="0.01"
                                    required
                                    class="price-input w-full
                                        rounded-lg border
                                        border-slate-300 bg-white
                                        px-3 py-2.5 text-sm
                                        text-slate-700 outline-none
                                        transition focus:border-blue-500
                                        focus:ring-2 focus:ring-blue-200"
                                >

                                @error(
                                    "items.$index.purchase_price"
                                )
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
                    class="mt-4 inline-flex w-full items-center
                        justify-center gap-2 rounded-lg
                        bg-slate-700 px-4 py-3 text-sm
                        font-semibold text-white transition
                        hover:bg-slate-800 sm:hidden"
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
                    href="{{ route('goods-receipts.index') }}"
                    class="rounded-lg border border-slate-300
                        bg-white px-5 py-2.5 text-center
                        text-sm font-semibold text-slate-600
                        transition hover:bg-slate-50"
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
                        gap-2 rounded-lg bg-blue-600 px-5
                        py-2.5 text-sm font-semibold text-white
                        transition hover:bg-blue-700
                        disabled:cursor-not-allowed
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
        <div
            class="item-row grid grid-cols-1 gap-4
                rounded-xl border border-slate-200
                bg-slate-50 p-4
                md:grid-cols-[minmax(0,2fr)_8rem_12rem_3rem]
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
                            data-price="{{ $item->purchase_price }}"
                        >
                            {{ $item->name }}
                            — {{ $item->unit }}
                            — stok
                            {{ number_format((int) $item->stock) }}
                            {{ $item->unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Jumlah barang. --}}
            <div>
                <label
                    class="mb-2 block text-xs font-semibold
                        uppercase tracking-wide text-slate-500
                        md:hidden"
                >
                    Jumlah
                </label>

                <input
                    type="number"
                    name="items[__INDEX__][quantity]"
                    value="1"
                    min="1"
                    step="1"
                    required
                    class="w-full rounded-lg border
                        border-slate-300 bg-white px-3
                        py-2.5 text-sm text-slate-700
                        outline-none transition
                        focus:border-blue-500 focus:ring-2
                        focus:ring-blue-200"
                >
            </div>

            {{-- Harga beli. --}}
            <div>
                <label
                    class="mb-2 block text-xs font-semibold
                        uppercase tracking-wide text-slate-500
                        md:hidden"
                >
                    Harga Beli
                </label>

                <input
                    type="number"
                    name="items[__INDEX__][purchase_price]"
                    min="0"
                    step="0.01"
                    required
                    class="price-input w-full rounded-lg
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
             * Mengisi harga beli berdasarkan barang terpilih.
             */
            function handleItemChange(event) {
                const select = event.currentTarget;
                const row = select.closest('.item-row');

                if (! row) {
                    return;
                }

                const priceInput =
                    row.querySelector('.price-input');

                const selectedOption = select.options[
                    select.selectedIndex
                ];

                if (! priceInput) {
                    return;
                }

                priceInput.value =
                    selectedOption?.dataset.price ?? '';
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
