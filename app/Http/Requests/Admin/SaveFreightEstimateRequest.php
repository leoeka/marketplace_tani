<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveFreightEstimateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $isSea = $this->input('mode') === 'sea';

        return [
            'mode' => ['required', Rule::in(['sea', 'air'])],
            'destination' => ['required', 'string', 'max:100'],
            'loading_port_id' => $isSea
                ? ['required', 'integer', Rule::exists('loading_ports', 'id')->where('is_active', true)]
                : ['prohibited_if:mode,air', 'nullable', 'integer'],
            'transit_days_min' => ['required', 'integer', 'between:1,365'],
            'transit_days_max' => ['required', 'integer', 'between:1,365'],
            'rate_usd_per_kg' => ['nullable', 'numeric', 'decimal:0,2', 'between:0,100000'],
            'note' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->hasAny(['transit_days_min', 'transit_days_max'])) {
                return;
            }

            if ((int) $this->input('transit_days_max') < (int) $this->input('transit_days_min')) {
                $validator->errors()->add(
                    'transit_days_max',
                    __('admin.logistics.estimates.validation.max_before_min', [], 'id'),
                );
            }
        }];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return __('admin.logistics.estimates.validation', [], 'id');
    }
}