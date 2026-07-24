<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoodsReceipt extends Model
{
    protected $fillable = [
        'supplier_id',
        'user_id',
        'received_at',
        'note',
    ];

    /**
     * Mengubah atribut tanggal ke objek tanggal.
     */
    protected function casts(): array
    {
        return [
            'received_at' => 'date',
        ];
    }

    /**
     * Mendapatkan supplier transaksi.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Mendapatkan pengguna yang mencatat transaksi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->withTrashed();
    }

    /**
     * Mendapatkan detail barang masuk.
     */
    public function details(): HasMany
    {
        return $this->hasMany(GoodsReceiptDetail::class);
    }
}
