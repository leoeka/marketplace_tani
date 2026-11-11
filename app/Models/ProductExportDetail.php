<?php

namespace App\Models;

use Database\Factories\ProductExportDetailFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductExportDetail extends Model
{
    /** @use HasFactory<ProductExportDetailFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name_en',
        'description_en',
        'hs_code',
        'moisture_percent',
        'size_spec',
        'monthly_capacity',
        'monthly_capacity_unit',
    ];

    protected function casts(): array
    {
        return [
            'moisture_percent' => 'decimal:2',
            'monthly_capacity' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}