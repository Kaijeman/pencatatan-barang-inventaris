<?php

namespace App\Http\Requests\StockOpname;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockOpnameRequest extends FormRequest
{
    /**
     * Menentukan apakah pengguna dapat membuat stock opname.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Mendefinisikan aturan validasi stock opname.
     */
    public function rules(): array
    {
        return [
            'item_id' => [
                'required',
                'integer',
                Rule::exists('items', 'id'),
            ],

            'physical_stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'opname_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Mendefinisikan pesan kesalahan validasi.
     */
    public function messages(): array
    {
        return [
            'item_id.required' => 'Barang wajib dipilih.',
            'item_id.exists' => 'Barang yang dipilih tidak ditemukan.',

            'physical_stock.required' => 'Stok fisik wajib diisi.',
            'physical_stock.integer' =>
                'Stok fisik harus berupa bilangan bulat.',
            'physical_stock.min' =>
                'Stok fisik tidak boleh bernilai negatif.',

            'opname_date.required' => 'Tanggal opname wajib diisi.',
            'opname_date.date' => 'Tanggal opname tidak valid.',
            'opname_date.before_or_equal' =>
                'Tanggal opname tidak boleh melewati hari ini.',

            'note.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
