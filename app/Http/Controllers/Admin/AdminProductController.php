<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminProductIndexRequest;
use App\Http\Requests\Admin\ApproveProductRequest;
use App\Http\Requests\Admin\RejectProductRequest;
use App\Http\Requests\Admin\SaveProductExportDetailRequest;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class AdminProductController extends Controller
{
    public function index(AdminProductIndexRequest $request): View
    {
        $status = $request->validated('status') ?? 'pending';

        return view('admin.products.index', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.products.title', [], 'id'),
            'status' => $status,
            'products' => Product::query()
                ->with(['farmer.farmerProfile', 'category', 'images'])
                ->when($status !== 'all', fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function show(Product $product): View
    {
        Gate::authorize('view', $product);
        $product->load(['farmer.farmerProfile', 'category', 'images', 'exportDetails']);
        $exportReasons = [];

        if ($product->status !== 'approved') {
            $exportReasons[] = 'not_approved';
        }

        if (! $product->show_in_export) {
            $exportReasons[] = 'channel_disabled';
        }

        if (blank($product->exportDetails?->name_en) || blank($product->exportDetails?->description_en)) {
            $exportReasons[] = 'details_incomplete';
        }

        if ($product->farmer->role !== 'farmer' || $product->farmer->farmerProfile?->verified_at === null) {
            $exportReasons[] = 'farmer_unverified';
        }

        return view('admin.products.show', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.products.detail_title', [], 'id'),
            'product' => $product,
            'exportReasons' => $exportReasons,
        ]);
    }

    public function saveExportDetails(SaveProductExportDetailRequest $request, Product $product): RedirectResponse
    {
        $product->exportDetails()->updateOrCreate(
            ['product_id' => $product->id],
            $request->validated(),
        );

        return redirect()->route('admin.products.show', $product)
            ->with('success', __('admin.products.export_details_saved', [], 'id'));
    }

    public function approve(ApproveProductRequest $request, Product $product): RedirectResponse
    {
        $product->update([
            'status' => 'approved',
            'rejection_note' => null,
            'show_in_marketplace' => $request->boolean('show_in_marketplace'),
            'show_in_export' => $request->boolean('show_in_export'),
        ]);

        return redirect()->route('admin.products.show', $product)
            ->with('success', __('admin.products.approved_success', [], 'id'));
    }

    public function updateChannels(ApproveProductRequest $request, Product $product): RedirectResponse
    {
        abort_unless($product->status === 'approved', 404);

        $product->update([
            'show_in_marketplace' => $request->boolean('show_in_marketplace'),
            'show_in_export' => $request->boolean('show_in_export'),
        ]);

        return redirect()->route('admin.products.show', $product)
            ->with('success', __('admin.products.channels_saved', [], 'id'));
    }

    public function reject(RejectProductRequest $request, Product $product): RedirectResponse
    {
        $product->update([
            'status' => 'rejected',
            'rejection_note' => $request->validated('rejection_note'),
            'show_in_marketplace' => false,
            'show_in_export' => false,
        ]);

        return redirect()->route('admin.products.show', $product)
            ->with('success', __('admin.products.rejected_success', [], 'id'));
    }
}