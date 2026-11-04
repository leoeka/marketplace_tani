<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class RejectProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $product = $this->route('product');

        return $product instanceof Product
            && ($this->user()?->can('moderate', $product) ?? false);
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'rejection_note' => ['required', 'string', 'max:2000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'rejection_note.required' => __('admin.validation.rejection_required', [], 'id'),
            'rejection_note.string' => __('admin.validation.rejection_string', [], 'id'),
            'rejection_note.max' => __('admin.validation.rejection_max', [], 'id'),
        ];
    }
}