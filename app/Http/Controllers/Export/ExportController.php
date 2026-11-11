<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Http\Requests\Export\ExportProductCatalogRequest;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ExportController extends Controller
{
    public function home(): View
    {
        return view('export.home', [
            'featuredProducts' => Product::exportVisible()
                ->with(['category', 'images', 'exportDetails'])
                ->latest()
                ->orderByDesc('id')
                ->limit(6)
                ->get(),
        ]);
    }

    public function index(ExportProductCatalogRequest $request): View
    {
        $filters = $request->validated();
        $products = Product::exportVisible()
            ->with(['category', 'images', 'exportDetails'])
            ->when($filters['category'] ?? null, fn (Builder $query, string $category): Builder => $query->whereHas(
                'category',
                fn (Builder $categoryQuery): Builder => $categoryQuery->where('slug', $category),
            ))
            ->when($filters['q'] ?? null, fn (Builder $query, string $search): Builder => $query->whereHas(
                'exportDetails',
                fn (Builder $detailsQuery): Builder => $detailsQuery->where('name_en', 'like', '%'.$search.'%'),
            ))
            ->latest()
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('export.products.index', [
            'products' => $products,
            'categories' => Category::query()->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function showProduct(string $slug): View
    {
        $product = Product::exportVisible()
            ->with([
                'category',
                'images',
                'exportDetails',
                'certificates' => fn ($query) => $query
                    ->whereNotNull('file_path')
                    ->notExpired(),
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('export.products.show', [
            'product' => $product,
        ]);
    }

    public function certifications(): View
    {
        $certificates = Certificate::query()
            ->whereNotNull('file_path')
            ->notExpired()
            ->whereHas('products', fn ($productQuery) => $productQuery->exportVisible())
            ->with(['products' => fn ($productQuery) => $productQuery
                ->exportVisible()
                ->with('exportDetails')])
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(12);

        return view('export.certifications', [
            'certificates' => $certificates,
        ]);
    }
}