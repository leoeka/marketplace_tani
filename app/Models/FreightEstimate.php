<?php

namespace App\Models;

use Database\Factories\FreightEstimateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreightEstimate extends Model
{
    /** @use HasFactory<FreightEstimateFactory> */
    use HasFactory;

    protected $fillable = [
        'mode',
        'destination',
        'loading_port_id',
        'transit_days_min',
        'transit_days_max',
        'rate_usd_per_kg',
        'note',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'transit_days_min' => 'integer',
            'transit_days_max' => 'integer',
            'rate_usd_per_kg' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function loadingPort(): BelongsTo
    {
        return $this->belongsTo(LoadingPort::class);
    }
}