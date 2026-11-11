<?php

namespace App\Http\Requests\Export;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExportProductCatalogRequest extends FormRequest
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
            'category.exists' => __('export.catalog.category_invalid', [], 'en'),
            'q.max' => __('export.catalog.search_max', [], 'en'),
        ];
    }
}