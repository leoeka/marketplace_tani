<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;

class IncomingOrderController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Order::class);
        $farmerId = auth()->id();

        return view('farmer.orders.index', [
            'menu' => config('dashboard.farmer_menu'),
            'title' => __('farmer.orders.title', [], 'id'),
            'orders' => Order::query()
                ->whereHas('items', fn ($query) => $query->where('farmer_id', $farmerId))
                ->with(['items' => fn ($query) => $query->where('farmer_id', $farmerId)])
                ->latest()
                ->paginate(12),
        ]);
    }

    public function show(Order $order): View
    {
        Gate::authorize('viewFarmerItems', $order);

        return view('farmer.orders.show', [
            'menu' => config('dashboard.farmer_menu'),
            'title' => __('farmer.orders.detail_title', [], 'id'),
            'order' => $order->load(['items' => fn ($query) => $query->where('farmer_id', auth()->id())]),
        ]);
    }
}
