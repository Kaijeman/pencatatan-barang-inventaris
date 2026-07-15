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

    protected $casts = [
        'issued_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(GoodsIssueDetail::class);
    }
}
