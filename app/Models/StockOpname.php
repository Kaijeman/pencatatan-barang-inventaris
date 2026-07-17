<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpname extends Model
{
    protected $fillable = [
        'item_id',
        'user_id',
        'system_stock',
        'physical_stock',
        'difference',
        'opname_date',
        'note',
    ];

    /**
     * Mengubah atribut model ke tipe data yang sesuai.
     */
    protected function casts(): array
    {
        return [
            'opname_date' => 'date',
            'system_stock' => 'integer',
            'physical_stock' => 'integer',
            'difference' => 'integer',
        ];
    }

    /**
     * Mendapatkan barang yang diperiksa.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Mendapatkan petugas yang melakukan opname.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
