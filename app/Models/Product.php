<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'category_id',
        'name',
        'slug',
        'description',
        'unit',
        'price_idr',
        'stock',
        'status',
        'rejection_note',
        'show_in_marketplace',
        'show_in_export',
    ];

    protected function casts(): array
    {
        return [
            'price_idr' => 'integer',
            'stock' => 'integer',
            'show_in_marketplace' => 'boolean',
            'show_in_export' => 'boolean',
        ];
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}