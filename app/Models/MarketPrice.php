<?php

namespace App\Models;

use Database\Factories\MarketPriceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketPrice extends Model
{
    /** @use HasFactory<MarketPriceFactory> */
    use HasFactory;

    protected $fillable = [
        'commodity',
        'price',
        'unit',
        'source',
        'recorded_on',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'recorded_on' => 'date',
        ];
    }
}
