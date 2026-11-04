<?php

namespace App\Http\Requests\Admin;

use App\Models\FarmerProfile;
use Illuminate\Foundation\Http\FormRequest;

class RevokeFarmerVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $farmerProfile = $this->route('farmerProfile');

        return $farmerProfile instanceof FarmerProfile
            && ($this->user()?->can('revokeVerification', $farmerProfile) ?? false);
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [];
    }
}