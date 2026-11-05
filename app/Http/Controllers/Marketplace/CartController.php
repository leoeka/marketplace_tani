<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketplace\AddToCartRequest;
use App\Http\Requests\Marketplace\UpdateCartItemRequest;
use App\Models\Product;
use App\Services\Marketplace\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    public function index(CartService $cart): View
    {
        return view('marketplace.cart.index', [
            'items' => $cart->items(),
            'total' => $cart->total(),
        ]);
    }

    public function store(AddToCartRequest $request, int $productId, CartService $cart): RedirectResponse
    {
        $product = Product::query()->with('farmer.farmerProfile')->findOrFail($productId);

        if (! $cart->add($product, (int) $request->validated('qty', 1))) {
            return back()->with('error', __('marketplace.cart.add_failed', [], 'id'));
        }

        return back()->with('success', __('marketplace.cart.added', [], 'id'));
    }

    public function update(UpdateCartItemRequest $request, int $productId, CartService $cart): RedirectResponse
    {
        if (! $cart->update($productId, (int) $request->validated('qty'))) {
            return back()->with('error', __('marketplace.cart.update_failed', [], 'id'));
        }

        return redirect()->route('cart.index')->with('success', __('marketplace.cart.updated', [], 'id'));
    }

    public function destroy(int $productId, CartService $cart): RedirectResponse
    {
        $cart->remove($productId);

        return redirect()->route('cart.index')->with('success', __('marketplace.cart.removed', [], 'id'));
    }
}
