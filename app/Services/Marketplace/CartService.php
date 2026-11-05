<?php

namespace App\Services\Marketplace;

use App\Models\Product;

class CartService
{
    /** @return array<int, array{product: Product|null, qty: int, eligible: bool, available: bool, line_total: int}> */
    public function items(): array
    {
        $cart = session('cart', []);
        $products = Product::query()
            ->whereIn('id', array_keys($cart))
            ->with(['farmer.farmerProfile', 'images'])
            ->get()
            ->keyBy('id');

        return collect($cart)->mapWithKeys(function (int $qty, int|string $productId) use ($products): array {
            $product = $products->get((int) $productId);
            $eligible = $product instanceof Product && $this->isAvailable($product);
            $available = $eligible && $product->stock >= $qty;

            return [(int) $productId => [
                'product' => $product,
                'qty' => $qty,
                'eligible' => $eligible,
                'available' => $available,
                'line_total' => $product instanceof Product ? $product->price_idr * $qty : 0,
            ]];
        })->all();
    }

    public function add(Product $product, int $qty): bool
    {
        if (! $this->isAvailable($product)) {
            return false;
        }

        $cart = session('cart', []);
        $newQty = ((int) ($cart[$product->id] ?? 0)) + $qty;

        if ($newQty > $product->stock) {
            return false;
        }

        $cart[$product->id] = $newQty;
        session(['cart' => $cart]);

        return true;
    }

    public function update(int $productId, int $qty): bool
    {
        $cart = session('cart', []);
        $product = Product::query()->with('farmer.farmerProfile')->find($productId);

        if (! array_key_exists($productId, $cart) || ! $product instanceof Product || ! $this->isAvailable($product) || $qty > $product->stock) {
            return false;
        }

        $cart[$productId] = $qty;
        session(['cart' => $cart]);

        return true;
    }

    public function remove(int $productId): void
    {
        $cart = session('cart', []);
        unset($cart[$productId]);
        session(['cart' => $cart]);
    }

    public function quantity(): int
    {
        return array_sum(array_map('intval', session('cart', [])));
    }

    public function total(): int
    {
        return collect($this->items())->sum('line_total');
    }

    public function isAvailable(Product $product): bool
    {
        return $product->status === 'approved'
            && $product->show_in_marketplace
            && $product->farmer?->role === 'farmer'
            && $product->farmer?->farmerProfile?->verified_at !== null;
    }
}