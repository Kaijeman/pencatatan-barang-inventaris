<?php

namespace App\Http\Requests\GoodsReceipt;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGoodsReceiptRequest extends FormRequest
{
    /**
     * Menentukan apakah pengguna dapat membuat barang masuk.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Mendefinisikan aturan validasi transaksi barang masuk.
     */
    public function rules(): array
    {
        return [
            'supplier_id' => [
                'required',
                'integer',
                Rule::exists('suppliers', 'id'),
            ],

            'received_at' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.item_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('items', 'id'),
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'items.*.purchase_price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }

    /**
     * Mendefinisikan pesan kesalahan validasi.
     */
    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Supplier wajib dipilih.',
            'supplier_id.exists' => 'Supplier yang dipilih tidak ditemukan.',

            'received_at.required' => 'Tanggal penerimaan wajib diisi.',
            'received_at.date' => 'Tanggal penerimaan tidak valid.',
            'received_at.before_or_equal' =>
                'Tanggal penerimaan tidak boleh melewati hari ini.',

            'note.max' => 'Catatan maksimal 1000 karakter.',

            'items.required' => 'Minimal satu barang harus ditambahkan.',
            'items.array' => 'Format data barang tidak valid.',
            'items.min' => 'Minimal satu barang harus ditambahkan.',

            'items.*.item_id.required' => 'Barang wajib dipilih.',
            'items.*.item_id.distinct' =>
                'Barang yang sama tidak boleh dipilih lebih dari satu kali.',
            'items.*.item_id.exists' =>
                'Barang yang dipilih tidak ditemukan.',

            'items.*.quantity.required' => 'Jumlah barang wajib diisi.',
            'items.*.quantity.integer' =>
                'Jumlah barang harus berupa bilangan bulat.',
            'items.*.quantity.min' =>
                'Jumlah barang minimal satu.',

            'items.*.purchase_price.required' =>
                'Harga beli wajib diisi.',
            'items.*.purchase_price.numeric' =>
                'Harga beli harus berupa angka.',
            'items.*.purchase_price.min' =>
                'Harga beli tidak boleh bernilai negatif.',
        ];
    }
}
