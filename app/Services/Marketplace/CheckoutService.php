<?php

namespace App\Services\Marketplace;

use App\Exceptions\CartCheckoutException;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(private CartService $cart)
    {
    }

    /** @param array<string, mixed> $data @param array<int, int> $cart */
    public function createOrder(User $customer, array $data, array $cart): Order
    {
        if ($customer->role !== 'customer' || ($data['payment_term'] === 'tempo' && $customer->customer_type !== 'business')) {
            throw new CartCheckoutException(__('marketplace.checkout.tempo_business_only', [], 'id'));
        }

        if ($cart === []) {
            throw new CartCheckoutException(__('marketplace.cart.empty', [], 'id'));
        }

        return DB::transaction(function () use ($customer, $data, $cart): Order {
            $productIds = array_map('intval', array_keys($cart));
            sort($productIds);
            $products = Product::query()
                ->whereIn('id', $productIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->with('farmer.farmerProfile')
                ->get()
                ->keyBy('id');
            $total = 0;

            foreach ($cart as $productId => $qty) {
                $product = $products->get((int) $productId);

                if (! $product instanceof Product || ! $this->cart->isAvailable($product) || (int) $qty < 1 || (int) $qty > $product->stock) {
                    throw new CartCheckoutException(__('marketplace.cart.item_unavailable', [], 'id'));
                }

                $total += $product->price_idr * (int) $qty;
            }

            $order = Order::query()->create([
                'customer_id' => $customer->id,
                'payment_method' => $data['payment_method'],
                'payment_term' => $data['payment_term'],
                'payment_status' => 'unpaid',
                'total' => $total,
                'shipping_address' => implode("\n", array_filter([
                    __('marketplace.checkout.address_recipient', ['value' => $data['recipient_name']], 'id'),
                    __('marketplace.checkout.address_phone', ['value' => $data['phone']], 'id'),
                    __('marketplace.checkout.address_detail', ['value' => $data['address']], 'id'),
                    filled($data['notes'] ?? null) ? __('marketplace.checkout.address_notes', ['value' => $data['notes']], 'id') : null,
                ])),
            ]);

            foreach ($cart as $productId => $qty) {
                $product = $products->get((int) $productId);
                $order->items()->create([
                    'product_id' => $product->id,
                    'farmer_id' => $product->farmer_id,
                    'product_name' => $product->name,
                    'unit' => $product->unit,
                    'price' => $product->price_idr,
                    'qty' => (int) $qty,
                ]);
                $product->decrement('stock', (int) $qty);
            }

            return $order;
        }, attempts: 3);
    }
}