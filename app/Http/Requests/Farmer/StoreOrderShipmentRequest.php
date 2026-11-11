<?php

namespace App\Http\Requests\Farmer;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');

        return $order instanceof Order
            && ($this->user()?->can('updateShipment', $order) ?? false);
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'tracking_number' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'tracking_number.required' => __('farmer.orders.validation.tracking_number_required', [], 'id'),
            'tracking_number.string' => __('farmer.orders.validation.tracking_number_string', [], 'id'),
            'tracking_number.max' => __('farmer.orders.validation.tracking_number_max', [], 'id'),
        ];
    }
}
