<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminMarketPriceIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'commodity' => ['nullable', 'string', 'max:255'],
        ];
    }
}
