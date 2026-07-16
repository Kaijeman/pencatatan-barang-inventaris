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
     * Mengubah tanggal opname menjadi objek tanggal.
     */
    protected function casts(): array
    {
        return [
            'opname_date' => 'date',
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
     * Mendapatkan pengguna yang mencatat stock opname.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
