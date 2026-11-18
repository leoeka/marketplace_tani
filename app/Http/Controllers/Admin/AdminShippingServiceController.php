<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveShippingServiceRequest;
use App\Models\ShippingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AdminShippingServiceController extends Controller
{
    public function index(): View
    {
        return view('admin.shipping-services.index', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.shipping_services.title'),
            'shippingServices' => ShippingService::query()->orderBy('name')->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.shipping-services.form', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.shipping_services.create_title'),
            'shippingService' => new ShippingService(['is_active' => true]),
            'action' => route('admin.shipping-services.store'),
            'method' => 'POST',
        ]);
    }

    public function store(SaveShippingServiceRequest $request): RedirectResponse
    {
        ShippingService::query()->create($request->validated());

        return redirect()->route('admin.shipping-services.index')
            ->with('success', __('admin.shipping_services.created'));
    }

    public function edit(ShippingService $shippingService): View
    {
        return view('admin.shipping-services.form', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.shipping_services.edit_title'),
            'shippingService' => $shippingService,
            'action' => route('admin.shipping-services.update', $shippingService),
            'method' => 'PUT',
        ]);
    }

    public function update(SaveShippingServiceRequest $request, ShippingService $shippingService): RedirectResponse
    {
        $shippingService->update($request->validated());

        return redirect()->route('admin.shipping-services.index')
            ->with('success', __('admin.shipping_services.updated'));
    }

    public function destroy(ShippingService $shippingService): RedirectResponse
    {
        $shippingService->delete();

        return redirect()->route('admin.shipping-services.index')
            ->with('success', __('admin.shipping_services.deleted'));
    }
}
