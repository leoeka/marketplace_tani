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
            'name_en.required' => __('admin.products.export_validation.name_required', [], 'id'),
            'name_en.string' => __('admin.products.export_validation.name_string', [], 'id'),
            'name_en.max' => __('admin.products.export_validation.name_max', [], 'id'),
            'description_en.required' => __('admin.products.export_validation.description_required', [], 'id'),
            'description_en.string' => __('admin.products.export_validation.description_string', [], 'id'),
            'hs_code.regex' => __('admin.products.export_validation.hs_code_invalid', [], 'id'),
            'moisture_percent.numeric' => __('admin.products.export_validation.moisture_numeric', [], 'id'),
            'moisture_percent.between' => __('admin.products.export_validation.moisture_range', [], 'id'),
            'size_spec.string' => __('admin.products.export_validation.size_string', [], 'id'),
            'size_spec.max' => __('admin.products.export_validation.size_max', [], 'id'),
            'monthly_capacity.required_with' => __('admin.products.export_validation.capacity_pair', [], 'id'),
            'monthly_capacity.integer' => __('admin.products.export_validation.capacity_integer', [], 'id'),
            'monthly_capacity.min' => __('admin.products.export_validation.capacity_min', [], 'id'),
            'monthly_capacity_unit.in' => __('admin.products.export_validation.capacity_unit', [], 'id'),
        ];
    }
}