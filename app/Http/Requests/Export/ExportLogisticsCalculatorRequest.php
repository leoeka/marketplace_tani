<?php

namespace App\Http\Requests\Export;

use App\Models\FreightEstimate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ExportLogisticsCalculatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function hasCalculatorInput(): bool
    {
        return $this->hasAny(['mode', 'loading_port_id', 'destination', 'weight', 'unit']);
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        if (! $this->hasCalculatorInput()) {
            return [];
        }

        $mode = $this->input('mode');
        $unit = $this->input('unit');
        $maximumWeight = $unit === 'ton' ? 1000 : 1000000;

        return [
            'mode' => ['required', Rule::in(['sea', 'air'])],
            'loading_port_id' => $mode === 'sea'
                ? ['required', 'integer', Rule::exists('loading_ports', 'id')->where('is_active', true)]
                : ['prohibited_if:mode,air', 'nullable', 'integer'],
            'destination' => [
                'required',
                'string',
                'max:100',
            ],
            'weight' => ['required', 'numeric', 'gt:0', 'lte:'.$maximumWeight],
            'unit' => ['required', Rule::in(['kg', 'ton'])],
        ];
    }

    public function after(): array
    {
        if (! $this->hasCalculatorInput()) {
            return [];
        }

        $mode = in_array($this->input('mode'), ['sea', 'air'], true) ? $this->input('mode') : 'sea';

        return [function (Validator $validator) use ($mode): void {
            if ($validator->errors()->hasAny(['mode', 'destination'])) {
                return;
            }

            $estimateQuery = FreightEstimate::query()
                ->where('mode', $mode)
                ->where('destination', $this->input('destination'))
                ->where('is_active', true);

            if ($mode === 'sea') {
                $estimateQuery->whereHas('loadingPort', fn ($query) => $query->where('is_active', true));
            }

            if (! $estimateQuery->exists()) {
                $validator->errors()->add('destination', $this->messages()['destination.exists'] ?? '');
            }
        }];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return __('export.logistics.validation');
    }
}