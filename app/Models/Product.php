<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'harvest_date',
    ];

    protected function casts(): array
    {
        return [
            'price_idr' => 'integer',
            'stock' => 'integer',
            'show_in_marketplace' => 'boolean',
            'show_in_export' => 'boolean',
            'harvest_date' => 'date',
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

    public function exportDetails(): HasOne
    {
        return $this->hasOne(ProductExportDetail::class);
    }

    public function certificates(): BelongsToMany
    {
        return $this->belongsToMany(Certificate::class, 'product_certificate')->orderBy('certificates.name');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPreorder(): bool
    {
        $harvestDate = $this->attributes['harvest_date'] ?? null;

        return is_string($harvestDate) && $harvestDate >= today()->toDateString();
    }

    public function scopePreorder(Builder $query): Builder
    {
        return $query->whereDate('harvest_date', '>=', today());
    }

    public function scopeExportVisible(Builder $query): Builder
    {
        return $query
            ->where('status', 'approved')
            ->where('show_in_export', true)
            ->whereHas('farmer', fn (Builder $farmerQuery): Builder => $farmerQuery
                ->where('role', 'farmer')
                ->whereHas('farmerProfile', fn (Builder $profileQuery): Builder => $profileQuery->whereNotNull('verified_at')))
            ->whereHas('exportDetails', fn (Builder $detailsQuery): Builder => $detailsQuery
                ->whereRaw("TRIM(name_en) <> ''")
                ->whereRaw("TRIM(description_en) <> ''"));
    }
}