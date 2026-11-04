<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'farmer'], true);
    }

    public function view(User $user, Product $product): bool
    {
        return $user->role === 'admin'
            || ($user->role === 'farmer' && $product->farmer_id === $user->id);
    }

    public function moderate(User $user, Product $product): bool
    {
        return $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'farmer';
    }

    public function update(User $user, Product $product): bool
    {
        return $this->view($user, $product);
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->view($user, $product);
    }

    public function submit(User $user, mixed $product = null): bool
    {
        return $user->role === 'farmer'
            && $user->farmerProfile()->whereNotNull('verified_at')->exists()
            && (! $product instanceof Product || $product->farmer_id === $user->id);
    }
}