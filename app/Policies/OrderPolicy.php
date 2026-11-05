<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'customer', 'farmer'], true);
    }

    public function view(User $user, Order $order): bool
    {
        return $user->role === 'admin'
            || ($user->role === 'customer' && $order->customer_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->role === 'customer';
    }

    public function viewFarmerItems(User $user, Order $order): bool
    {
        return $user->role === 'farmer'
            && $order->items()->where('farmer_id', $user->id)->exists();
    }

    public function markPaid(User $user, Order $order): bool
    {
        return $user->role === 'admin' && $order->payment_status === 'unpaid';
    }
}
