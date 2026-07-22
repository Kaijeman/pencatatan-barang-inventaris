<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'unit',
        'purchase_price',
        'stock',
        'minimum_stock',
        'description',
    ];

    /**
     * Mengubah atribut model ke tipe data yang sesuai.
     */
    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'stock' => 'integer',
            'minimum_stock' => 'integer',
        ];
    }

    /**
     * Mendapatkan kategori barang.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Mendapatkan detail transaksi barang masuk.
     */
    public function receiptDetails(): HasMany
    {
        return $this->hasMany(GoodsReceiptDetail::class);
    }

    /**
     * Mendapatkan detail transaksi barang keluar.
     */
    public function issueDetails(): HasMany
    {
        return $this->hasMany(GoodsIssueDetail::class);
    }
}
