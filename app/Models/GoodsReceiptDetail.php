<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceiptDetail extends Model
{
    protected $fillable = [
        'goods_receipt_id',
        'item_id',
        'quantity',
        'purchase_price',
    ];

    /**
     * Mengubah nilai harga menjadi tipe desimal.
     */
    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
        ];
    }

    /**
     * Mendapatkan transaksi utama dari detail barang masuk.
     */
    public function receipt(): BelongsTo
    {
        return $this->belongsTo(
            GoodsReceipt::class,
            'goods_receipt_id'
        );
    }

    /**
     * Mendapatkan barang dari detail transaksi.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
