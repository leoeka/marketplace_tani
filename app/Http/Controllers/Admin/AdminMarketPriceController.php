<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminMarketPriceIndexRequest;
use App\Http\Requests\Admin\StoreMarketPriceRequest;
use App\Http\Requests\Admin\UpdateMarketPriceRequest;
use App\Models\MarketPrice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AdminMarketPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(AdminMarketPriceIndexRequest $request): View
    {
        $filters = $request->validated();

        return view('admin.market-prices.index', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.market_prices.title', [], 'id'),
            'filters' => $filters,
            'prices' => MarketPrice::query()
                ->when($filters['commodity'] ?? null, fn ($query, string $commodity) => $query->where('commodity', 'like', '%'.$commodity.'%'))
                ->orderByDesc('recorded_on')
                ->orderByDesc('id')
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.market-prices.form', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.market_prices.create_title', [], 'id'),
            'price' => new MarketPrice(['recorded_on' => today()]),
            'action' => route('admin.market-prices.store'),
            'method' => 'POST',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMarketPriceRequest $request): RedirectResponse
    {
        MarketPrice::query()->create($request->validated());

        return redirect()->route('admin.market-prices.index')
            ->with('success', __('admin.market_prices.created', [], 'id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarketPrice $marketPrice): View
    {
        return view('admin.market-prices.form', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.market_prices.edit_title', [], 'id'),
            'price' => $marketPrice,
            'action' => route('admin.market-prices.update', $marketPrice),
            'method' => 'PUT',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMarketPriceRequest $request, MarketPrice $marketPrice): RedirectResponse
    {
        $marketPrice->update($request->validated());

        return redirect()->route('admin.market-prices.index')
            ->with('success', __('admin.market_prices.updated', [], 'id'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MarketPrice $marketPrice): RedirectResponse
    {
        $marketPrice->delete();

        return redirect()->route('admin.market-prices.index')
            ->with('success', __('admin.market_prices.deleted', [], 'id'));
    }
}
