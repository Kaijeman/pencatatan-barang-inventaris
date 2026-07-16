<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsIssueDetail extends Model
{
    protected $fillable = [
        'goods_issue_id',
        'item_id',
        'quantity',
    ];

    /**
     * Mendapatkan transaksi utama dari detail barang keluar.
     */
    public function issue(): BelongsTo
    {
        return $this->belongsTo(
            GoodsIssue::class,
            'goods_issue_id'
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
