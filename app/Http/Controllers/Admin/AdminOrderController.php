<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class AdminOrderController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Order::class);

        return view('admin.orders.index', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.orders.title', [], 'id'),
            'orders' => Order::query()->with('customer')->withCount('items')->latest()->paginate(12),
        ]);
    }

    public function show(Order $order): View
    {
        Gate::authorize('view', $order);

        return view('admin.orders.show', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.orders.detail_title', [], 'id'),
            'order' => $order->load(['customer', 'items.farmer']),
        ]);
    }

    public function markPaid(Order $order): RedirectResponse
    {
        Gate::authorize('markPaid', $order);
        $order->update(['payment_status' => 'paid']);

        return redirect()->route('admin.orders.show', $order)->with('success', __('admin.orders.paid_success', [], 'id'));
    }
}
