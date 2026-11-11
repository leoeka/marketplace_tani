<?php

namespace App\Models;

use Database\Factories\RfqFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rfq extends Model
{
    /** @use HasFactory<RfqFactory> */
    use HasFactory;

    protected $fillable = [
        'buyer_name',
        'company',
        'country',
        'email',
        'phone',
        'product_id',
        'product_name',
        'quantity',
        'quantity_unit',
        'wants_sample',
        'asks_moq',
        'incoterms',
        'message',
        'status',
        'handled_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'wants_sample' => 'boolean',
            'asks_moq' => 'boolean',
            'handled_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
}