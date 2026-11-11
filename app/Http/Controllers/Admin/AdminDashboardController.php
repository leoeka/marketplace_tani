<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\Rfq;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        Gate::authorize('viewAny', FarmerProfile::class);

        $farmerProfiles = FarmerProfile::query()->whereHas('user', fn ($query) => $query->where('role', 'farmer'));
        $productCounts = Product::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return view('admin.dashboard', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.dashboard.title', [], 'id'),
            'verifiedFarmers' => (clone $farmerProfiles)->whereNotNull('verified_at')->count(),
            'pendingFarmers' => (clone $farmerProfiles)->whereNull('verified_at')->count(),
            'pendingProducts' => Product::query()->where('status', 'pending')->count(),
            'newRfqs' => Rfq::query()->status('new')->count(),
            'productCounts' => collect(['draft', 'pending', 'approved', 'rejected'])
                ->mapWithKeys(fn (string $status): array => [$status => (int) ($productCounts[$status] ?? 0)]),
        ]);
    }
}