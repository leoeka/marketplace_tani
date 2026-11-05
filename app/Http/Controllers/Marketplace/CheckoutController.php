<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Exceptions\CartCheckoutException;
use App\Http\Requests\Marketplace\CheckoutRequest;
use App\Models\Order;
use App\Services\Marketplace\CartService;
use App\Services\Marketplace\CheckoutService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class CheckoutController extends Controller
{
    public function index(CartService $cart): View|RedirectResponse
    {
        Gate::authorize('create', Order::class);

        if ($cart->items() === []) {
            return redirect()->route('cart.index')->with('error', __('marketplace.cart.empty', [], 'id'));
        }

        return view('marketplace.checkout.index', [
            'items' => $cart->items(),
            'total' => $cart->total(),
        ]);
    }

    public function store(CheckoutRequest $request, CheckoutService $checkout): RedirectResponse
    {
        try {
            $order = $checkout->createOrder($request->user(), $request->validated(), session('cart', []));
        } catch (CartCheckoutException $exception) {
            return redirect()->route('cart.index')->with('error', $exception->getMessage());
        }

        session()->forget('cart');

        return redirect()->route('customer.orders.show', $order)->with('success', __('marketplace.checkout.created', [], 'id'));
    }
}
