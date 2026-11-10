<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMarketPriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'commodity' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:1'],
            'unit' => ['required', Rule::in(['kg', 'ton'])],
            'source' => ['required', 'string', 'max:255'],
            'recorded_on' => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
