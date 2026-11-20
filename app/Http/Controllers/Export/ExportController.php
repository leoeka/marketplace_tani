<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function home(): View
    {
        return view('export.home', [
            // Produk diambil dari marketplace; detailnya dibuka di website marketplace.
            'featuredProducts' => Product::query()
                ->marketplaceVisible()
                ->with(['category', 'images', 'exportDetails'])
                ->latest()
                ->orderByDesc('id')
                ->limit(6)
                ->get(),
        ]);
    }

    /** Katalog produk hanya ada di marketplace. */
    public function index(Request $request): RedirectResponse
    {
        $category = $request->query('category');

        return redirect()->route(
            'marketplace.products.index',
            is_string($category) && $category !== '' ? ['category' => $category] : [],
            301,
        );
    }

    /** Detail produk hanya ada di marketplace. */
    public function showProduct(string $slug): RedirectResponse
    {
        return redirect()->route('marketplace.products.show', $slug, 301);
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
