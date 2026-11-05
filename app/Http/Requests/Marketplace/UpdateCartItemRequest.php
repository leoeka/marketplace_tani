<?php

namespace App\Http\Requests\Marketplace;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'qty' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'qty.required' => __('marketplace.cart.validation.qty_required', [], 'id'),
            'qty.integer' => __('marketplace.cart.validation.qty_integer', [], 'id'),
            'qty.min' => __('marketplace.cart.validation.qty_min', [], 'id'),
        ];
    }
}
