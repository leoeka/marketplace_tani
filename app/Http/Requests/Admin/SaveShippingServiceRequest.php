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
            'name.required' => __('admin.shipping_services.validation.name_required', [], 'id'),
            'name.string' => __('admin.shipping_services.validation.name_string', [], 'id'),
            'name.max' => __('admin.shipping_services.validation.name_max', [], 'id'),
            'type.required' => __('admin.shipping_services.validation.type_required', [], 'id'),
            'type.in' => __('admin.shipping_services.validation.type_invalid', [], 'id'),
            'is_active.required' => __('admin.shipping_services.validation.is_active_required', [], 'id'),
            'is_active.boolean' => __('admin.shipping_services.validation.is_active_invalid', [], 'id'),
        ];
    }
}
