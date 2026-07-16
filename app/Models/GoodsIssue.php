<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoodsIssue extends Model
{
    protected $fillable = [
        'issue_number',
        'user_id',
        'destination',
        'issued_at',
        'note',
    ];

    /**
     * Mengubah tanggal pengeluaran menjadi objek tanggal.
     */
    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
        ];
    }

    /**
     * Mendapatkan pengguna yang mencatat transaksi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan seluruh detail transaksi barang keluar.
     */
    public function details(): HasMany
    {
        return $this->hasMany(GoodsIssueDetail::class);
    }
}
