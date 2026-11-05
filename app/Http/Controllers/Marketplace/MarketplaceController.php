<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketplace\ProductCatalogRequest;
use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class MarketplaceController extends Controller
{
    public function home(): View
    {
        return view('welcome', [
            'products' => $this->marketplaceProducts()->latest()->limit(8)->get(),
            'farmers' => FarmerProfile::query()
                ->whereNotNull('verified_at')
                ->whereHas('user', fn (Builder $query) => $query->where('role', 'farmer'))
                ->with('user')
                ->latest('verified_at')
                ->limit(3)
                ->get(),
        ]);
    }

    public function index(ProductCatalogRequest $request): View
    {
        $filters = $request->validated();
        $products = $this->marketplaceProducts()
            ->when($filters['category'] ?? null, fn (Builder $query, string $category): Builder => $query->whereHas(
                'category',
                fn (Builder $categoryQuery): Builder => $categoryQuery->where('slug', $category),
            ))
            ->when($filters['q'] ?? null, fn (Builder $query, string $search): Builder => $query->where('name', 'like', '%'.$search.'%'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('marketplace.products.index', [
            'products' => $products,
            'categories' => Category::query()->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function preorder(ProductCatalogRequest $request): View
    {
        $filters = $request->validated();
        $products = $this->marketplaceProducts()
            ->preorder()
            ->when($filters['category'] ?? null, fn (Builder $query, string $category): Builder => $query->whereHas(
                'category',
                fn (Builder $categoryQuery): Builder => $categoryQuery->where('slug', $category),
            ))
            ->when($filters['q'] ?? null, fn (Builder $query, string $search): Builder => $query->where('name', 'like', '%'.$search.'%'))
            ->orderBy('harvest_date')
            ->orderBy('id')
            ->paginate(12)
            ->withQueryString();

        return view('marketplace.products.index', [
            'products' => $products,
            'categories' => Category::query()->orderBy('name')->get(),
            'filters' => $filters,
            'catalogTitle' => __('marketplace.preorder.title', [], 'id'),
            'catalogDescription' => __('marketplace.preorder.description', [], 'id'),
            'emptyMessage' => __('marketplace.preorder.empty', [], 'id'),
            'catalogRoute' => route('marketplace.preorders.index'),
        ]);
    }

    public function showProduct(string $slug): View
    {
        $product = $this->marketplaceProducts()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('marketplace.products.show', ['product' => $product]);
    }

    public function showFarmer(string $slug): View
    {
        $farmerProfile = FarmerProfile::query()
            ->where('slug', $slug)
            ->whereNotNull('verified_at')
            ->whereHas('user', fn (Builder $query) => $query->where('role', 'farmer'))
            ->with('user')
            ->firstOrFail();

        $products = $this->marketplaceProducts()
            ->where('farmer_id', $farmerProfile->user_id)
            ->latest()
            ->paginate(12);

        return view('marketplace.farmers.show', compact('farmerProfile', 'products'));
    }

    private function marketplaceProducts(): Builder
    {
        return Product::query()
            ->where('status', 'approved')
            ->where('show_in_marketplace', true)
            ->whereHas('farmer', fn (Builder $query) => $query
                ->where('role', 'farmer')
                ->whereHas('farmerProfile', fn (Builder $profileQuery) => $profileQuery->whereNotNull('verified_at')))
            ->with(['category', 'images', 'farmer.farmerProfile']);
    }
}