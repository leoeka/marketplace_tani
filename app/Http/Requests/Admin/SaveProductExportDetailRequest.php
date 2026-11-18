<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveProductExportDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'name_en' => ['required', 'string', 'max:255'],
            'description_en' => ['required', 'string'],
            'hs_code' => ['nullable', 'string', 'regex:/^(?=.*[0-9])[0-9.]{4,13}$/'],
            'moisture_percent' => ['nullable', 'numeric', 'between:0,100'],
            'size_spec' => ['nullable', 'string', 'max:255'],
            'monthly_capacity' => ['nullable', 'required_with:monthly_capacity_unit', 'integer', 'min:0'],
            'monthly_capacity_unit' => ['nullable', 'required_with:monthly_capacity', 'in:kg,ton'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name_en.required' => __('admin.products.export_validation.name_required'),
            'name_en.string' => __('admin.products.export_validation.name_string'),
            'name_en.max' => __('admin.products.export_validation.name_max'),
            'description_en.required' => __('admin.products.export_validation.description_required'),
            'description_en.string' => __('admin.products.export_validation.description_string'),
            'hs_code.regex' => __('admin.products.export_validation.hs_code_invalid'),
            'moisture_percent.numeric' => __('admin.products.export_validation.moisture_numeric'),
            'moisture_percent.between' => __('admin.products.export_validation.moisture_range'),
            'size_spec.string' => __('admin.products.export_validation.size_string'),
            'size_spec.max' => __('admin.products.export_validation.size_max'),
            'monthly_capacity.required_with' => __('admin.products.export_validation.capacity_pair'),
            'monthly_capacity.integer' => __('admin.products.export_validation.capacity_integer'),
            'monthly_capacity.min' => __('admin.products.export_validation.capacity_min'),
            'monthly_capacity_unit.in' => __('admin.products.export_validation.capacity_unit'),
        ];
    }
}