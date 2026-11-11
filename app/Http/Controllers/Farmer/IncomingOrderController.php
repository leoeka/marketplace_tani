<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\StoreOrderShipmentRequest;
use App\Models\Order;
use App\Models\OrderShipment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
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
            'order' => $order->load([
                'items' => fn ($query) => $query->where('farmer_id', auth()->id()),
                'shipments' => fn ($query) => $query->where('farmer_id', auth()->id()),
            ]),
        ]);
    }

    public function saveShipment(StoreOrderShipmentRequest $request, Order $order): RedirectResponse
    {
        $shipment = OrderShipment::query()->firstOrNew([
            'order_id' => $order->id,
            'farmer_id' => $request->user()->id,
        ]);
        $shippedAt = $shipment->exists ? $shipment->shipped_at : now();
        $shipment->fill($request->validated());
        $shipment->shipped_at = $shippedAt;
        $shipment->save();

        return redirect()->route('farmer.orders.show', $order)
            ->with('success', __('farmer.orders.shipment_saved', [], 'id'));
    }
}
