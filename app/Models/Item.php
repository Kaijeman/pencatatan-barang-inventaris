<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'category_id',
        'code',
        'name',
        'unit',
        'purchase_price',
        'stock',
        'minimum_stock',
        'description',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function receiptDetails(): HasMany
    {
        return $this->hasMany(GoodsReceiptDetail::class);
    }

    public function issueDetails(): HasMany
    {
        return $this->hasMany(GoodsIssueDetail::class);
    }

    public function stockOpnames(): HasMany
    {
        return $this->hasMany(StockOpname::class);
    }
}
