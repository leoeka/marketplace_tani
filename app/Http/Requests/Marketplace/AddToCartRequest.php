<?php

namespace App\Http\Requests\Marketplace;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'qty' => ['sometimes', 'required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'qty.integer' => __('marketplace.cart.validation.qty_integer', [], 'id'),
            'qty.min' => __('marketplace.cart.validation.qty_min', [], 'id'),
        ];
    }
}
