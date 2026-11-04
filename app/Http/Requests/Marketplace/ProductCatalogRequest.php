<?php

namespace App\Http\Requests\Marketplace;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductCatalogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'category' => ['nullable', 'string', Rule::exists('categories', 'slug')],
            'q' => ['nullable', 'string', 'max:100'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'category.exists' => __('marketplace.catalog.category_invalid', [], 'id'),
            'q.max' => __('marketplace.catalog.search_max', [], 'id'),
        ];
    }
}