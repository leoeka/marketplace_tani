<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;

class CustomerOrderController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Order::class);

        return view('marketplace.orders.index', [
            'orders' => Order::query()
                ->where('customer_id', auth()->id())
                ->withCount('items')
                ->latest()
                ->paginate(12),
        ]);
    }

    public function show(Order $order): View
    {
        Gate::authorize('view', $order);

        return view('marketplace.orders.show', [
            'order' => $order->load('items'),
        ]);
    }
}
