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

    public function issue(): BelongsTo
    {
        return $this->belongsTo(GoodsIssue::class, 'goods_issue_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
