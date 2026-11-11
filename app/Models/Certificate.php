<?php

namespace App\Models;

use Database\Factories\CertificateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Certificate extends Model
{
    public const TYPES = [
        'organic',
        'halal',
        'fair_trade',
        'haccp',
        'iso',
        'bpom',
        'phytosanitary_certificate',
        'other',
    ];

    /** @use HasFactory<CertificateFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'issuer',
        'certificate_number',
        'expires_at',
        'file_path',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'date',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_certificate');
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(fn (Builder $certificateQuery): Builder => $certificateQuery
            ->whereNull('expires_at')
            ->orWhereDate('expires_at', '>=', today()));
    }
}