<?php

namespace App\Http\Requests\Export;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExportRfqRequest extends FormRequest
{
    /** @var array<int, string> */
    private const TEXT_FIELDS = ['buyer_name', 'company', 'country', 'email', 'phone', 'message', 'fax_number'];

    protected function prepareForValidation(): void
    {
        $cleaned = [];

        foreach (self::TEXT_FIELDS as $field) {
            $value = $this->input($field);

            if (is_string($value)) {
                $trimmedValue = trim($value);
                $cleaned[$field] = $field === 'message'
                    ? $trimmedValue
                    : (preg_replace('/[\r\n\x00-\x1F\x7F]/u', '', $trimmedValue) ?? $trimmedValue);
            }
        }

        $this->merge($cleaned);

        $productId = $this->input('product_id');

        if (is_numeric($productId) && ! Product::exportVisible()->whereKey($productId)->exists()) {
            $this->merge(['product_id' => null]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->filled('fax_number')) {
            return [];
        }

        return [
            'buyer_name' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'product_id' => ['nullable', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'quantity_unit' => [
                'nullable',
                'required_with:quantity',
                Rule::in(['kg', 'ton']),
            ],
            'wants_sample' => ['nullable', 'boolean'],
            'asks_moq' => ['nullable', 'boolean'],
            'incoterms' => ['required', Rule::in(['FOB', 'CIF', 'EXW', 'TBD'])],
            'message' => ['nullable', 'string', 'max:2000'],
            'fax_number' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return __('export.rfq.attributes');
    }

    public function isHoneypotFilled(): bool
    {
        return $this->filled('fax_number');
    }
}