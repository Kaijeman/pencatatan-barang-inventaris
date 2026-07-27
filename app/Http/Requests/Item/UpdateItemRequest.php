<?php

namespace App\Http\Requests\Item;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
{
    /**
     * Menentukan izin request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Menentukan aturan validasi perubahan barang.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'unit' => [
                'required',
                'string',
                'max:50',
            ],
            'purchase_price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'minimum_stock' => [
                'required',
                'integer',
                'min:0',
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ];
    }
}
