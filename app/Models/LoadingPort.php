<?php

namespace App\Models;

use Database\Factories\LoadingPortFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoadingPort extends Model
{
    /** @use HasFactory<LoadingPortFactory> */
    use HasFactory;

    protected $fillable = ['name', 'city', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function freightEstimates(): HasMany
    {
        return $this->hasMany(FreightEstimate::class);
    }
}