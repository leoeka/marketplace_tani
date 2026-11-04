<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class FarmerDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $counts = $request->user()->products()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return view('farmer.dashboard', [
            'menu' => config('dashboard.farmer_menu'),
            'title' => __('farmer.dashboard.title', [], 'id'),
            'counts' => collect(['draft', 'pending', 'approved', 'rejected'])
                ->mapWithKeys(fn (string $status): array => [$status => (int) ($counts[$status] ?? 0)]),
            'isVerified' => $request->user()->farmerProfile()->whereNotNull('verified_at')->exists(),
            'recentProducts' => $request->user()->products()->with('category')->latest()->limit(3)->get(),
        ]);
    }
}