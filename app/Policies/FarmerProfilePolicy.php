<?php

namespace App\Policies;

use App\Models\FarmerProfile;
use App\Models\User;

class FarmerProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, FarmerProfile $farmerProfile): bool
    {
        return $user->role === 'admin';
    }

    public function verify(User $user, FarmerProfile $farmerProfile): bool
    {
        return $user->role === 'admin';
    }

    public function revokeVerification(User $user, FarmerProfile $farmerProfile): bool
    {
        return $user->role === 'admin';
    }
}