<?php

namespace App\Http\Requests\Admin;

use App\Models\Certificate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'type' => ['required', 'string', Rule::in(Certificate::TYPES)],
            'issuer' => ['nullable', 'string', 'max:255'],
            'certificate_number' => ['nullable', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date_format:Y-m-d'],
            'file' => [
                $this->isMethod('post') || blank($this->route('certificate')?->file_path) ? 'required' : 'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'mimetypes:application/pdf,image/jpeg,image/png',
                'max:5120',
            ],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'distinct', 'exists:products,id'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => __('admin.certificates.validation.name_required'),
            'name.string' => __('admin.certificates.validation.name_string'),
            'name.max' => __('admin.certificates.validation.name_max'),
            'type.required' => __('admin.certificates.validation.type_required'),
            'type.in' => __('admin.certificates.validation.type_invalid'),
            'issuer.string' => __('admin.certificates.validation.issuer_string'),
            'issuer.max' => __('admin.certificates.validation.issuer_max'),
            'certificate_number.string' => __('admin.certificates.validation.number_string'),
            'certificate_number.max' => __('admin.certificates.validation.number_max'),
            'expires_at.date_format' => __('admin.certificates.validation.expires_at_invalid'),
            'file.required' => __('admin.certificates.validation.file_required'),
            'file.file' => __('admin.certificates.validation.file_invalid'),
            'file.mimes' => __('admin.certificates.validation.file_type'),
            'file.mimetypes' => __('admin.certificates.validation.file_type'),
            'file.max' => __('admin.certificates.validation.file_max'),
            'product_ids.array' => __('admin.certificates.validation.products_invalid'),
            'product_ids.*.integer' => __('admin.certificates.validation.product_invalid'),
            'product_ids.*.distinct' => __('admin.certificates.validation.products_duplicate'),
            'product_ids.*.exists' => __('admin.certificates.validation.product_invalid'),
        ];
    }
}