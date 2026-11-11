<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderShipment extends Model
{
    /** @use HasFactory<\Database\Factories\OrderShipmentFactory> */
    use HasFactory;

    protected $fillable = ['order_id', 'farmer_id', 'tracking_number', 'shipped_at'];

    protected function casts(): array
    {
        return ['shipped_at' => 'datetime'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }
}
