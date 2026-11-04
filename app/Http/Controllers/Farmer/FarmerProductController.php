<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\StoreProductRequest;
use App\Http\Requests\Farmer\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\Farmer\ProductCatalogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class FarmerProductController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Product::class);

        return view('farmer.products.index', [
            'menu' => config('dashboard.farmer_menu'),
            'title' => __('farmer.products.title', [], 'id'),
            'products' => Product::query()
                ->where('farmer_id', auth()->id())
                ->with(['category', 'images'])
                ->latest()
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Product::class);

        return view('farmer.products.form', [
            'menu' => config('dashboard.farmer_menu'),
            'title' => __('farmer.products.create_title', [], 'id'),
            'product' => new Product(),
            'categories' => Category::query()->orderBy('name')->get(),
            'isEditing' => false,
        ]);
    }

    public function store(StoreProductRequest $request, ProductCatalogService $products): RedirectResponse
    {
        $data = $request->validated();
        $action = $data['action'];

        if ($action === 'submit') {
            Gate::authorize('submit', Product::class);
        }

        $products->create($request->user(), $data, $action);

        return redirect()->route('farmer.products.index')->with('success', __('farmer.products.created', [], 'id'));
    }

    public function edit(Product $product): View
    {
        Gate::authorize('update', $product);

        return view('farmer.products.form', [
            'menu' => config('dashboard.farmer_menu'),
            'title' => __('farmer.products.edit_title', [], 'id'),
            'product' => $product->load('images'),
            'categories' => Category::query()->orderBy('name')->get(),
            'isEditing' => true,
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product, ProductCatalogService $products): RedirectResponse
    {
        $data = $request->validated();
        $action = $data['action'];

        if ($action === 'submit') {
            Gate::authorize('submit', $product);
        }

        $products->update($product, $data, $action);

        return redirect()->route('farmer.products.index')->with('success', __('farmer.products.updated', [], 'id'));
    }

    public function destroy(Product $product, ProductCatalogService $products): RedirectResponse
    {
        Gate::authorize('delete', $product);

        $products->delete($product);

        return redirect()->route('farmer.products.index')->with('success', __('farmer.products.deleted', [], 'id'));
    }

}