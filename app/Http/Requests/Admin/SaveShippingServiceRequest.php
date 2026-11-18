<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveShippingServiceRequest extends FormRequest
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
            'type' => ['required', Rule::in(['local_courier', 'cold_chain'])],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('admin.shipping_services.validation.name_required'),
            'name.string' => __('admin.shipping_services.validation.name_string'),
            'name.max' => __('admin.shipping_services.validation.name_max'),
            'type.required' => __('admin.shipping_services.validation.type_required'),
            'type.in' => __('admin.shipping_services.validation.type_invalid'),
            'is_active.required' => __('admin.shipping_services.validation.is_active_required'),
            'is_active.boolean' => __('admin.shipping_services.validation.is_active_invalid'),
        ];
    }
}
