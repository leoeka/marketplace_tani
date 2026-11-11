<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'file' => [
                $this->isMethod('post') || blank($this->route('certificate')?->file_path) ? 'required' : 'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'extensions:pdf,jpg,jpeg,png',
                'max:10240',
            ],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'distinct', 'exists:products,id'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => __('admin.certificates.validation.name_required', [], 'id'),
            'name.string' => __('admin.certificates.validation.name_string', [], 'id'),
            'name.max' => __('admin.certificates.validation.name_max', [], 'id'),
            'file.required' => __('admin.certificates.validation.file_required', [], 'id'),
            'file.file' => __('admin.certificates.validation.file_invalid', [], 'id'),
            'file.mimes' => __('admin.certificates.validation.file_type', [], 'id'),
            'file.extensions' => __('admin.certificates.validation.file_type', [], 'id'),
            'file.max' => __('admin.certificates.validation.file_max', [], 'id'),
            'product_ids.array' => __('admin.certificates.validation.products_invalid', [], 'id'),
            'product_ids.*.integer' => __('admin.certificates.validation.product_invalid', [], 'id'),
            'product_ids.*.distinct' => __('admin.certificates.validation.products_duplicate', [], 'id'),
            'product_ids.*.exists' => __('admin.certificates.validation.product_invalid', [], 'id'),
        ];
    }
}