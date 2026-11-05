<?php

namespace App\Http\Requests\Marketplace;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'customer';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', Rule::in(['transfer', 'ewallet', 'qris'])],
            'payment_term' => ['required', Rule::in(['cash', 'tempo'])],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($this->input('payment_term') === 'tempo' && $this->user()?->customer_type !== 'business') {
                $validator->errors()->add('payment_term', __('marketplace.checkout.tempo_business_only', [], 'id'));
            }
        }];
    }

    public function messages(): array
    {
        return [
            'recipient_name.required' => __('marketplace.checkout.validation.recipient_required', [], 'id'),
            'phone.required' => __('marketplace.checkout.validation.phone_required', [], 'id'),
            'address.required' => __('marketplace.checkout.validation.address_required', [], 'id'),
            'payment_method.required' => __('marketplace.checkout.validation.payment_method_required', [], 'id'),
            'payment_method.in' => __('marketplace.checkout.validation.payment_method_invalid', [], 'id'),
            'payment_term.required' => __('marketplace.checkout.validation.payment_term_required', [], 'id'),
            'payment_term.in' => __('marketplace.checkout.validation.payment_term_invalid', [], 'id'),
        ];
    }
}
