<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RevokeFarmerVerificationRequest;
use App\Http\Requests\Admin\VerifyFarmerRequest;
use App\Models\FarmerProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class AdminFarmerController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', FarmerProfile::class);

        return view('admin.farmers.index', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.farmers.title', [], 'id'),
            'farmers' => FarmerProfile::query()
                ->whereHas('user', fn ($query) => $query->where('role', 'farmer'))
                ->with('user')
                ->orderByRaw('verified_at is null desc')
                ->orderBy('shop_name')
                ->paginate(12),
        ]);
    }

    public function show(FarmerProfile $farmerProfile): View
    {
        Gate::authorize('view', $farmerProfile);

        abort_unless($farmerProfile->user()->where('role', 'farmer')->exists(), 404);

        return view('admin.farmers.show', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.farmers.detail_title', [], 'id'),
            'farmerProfile' => $farmerProfile->load('user'),
        ]);
    }

    public function verify(VerifyFarmerRequest $request, FarmerProfile $farmerProfile): RedirectResponse
    {
        abort_unless($farmerProfile->user()->where('role', 'farmer')->exists(), 404);

        $farmerProfile->update(['verified_at' => now()]);

        return back()->with('success', __('admin.farmers.verified_success', [], 'id'));
    }

    public function revoke(RevokeFarmerVerificationRequest $request, FarmerProfile $farmerProfile): RedirectResponse
    {
        abort_unless($farmerProfile->user()->where('role', 'farmer')->exists(), 404);

        $farmerProfile->update(['verified_at' => null]);

        return back()->with('success', __('admin.farmers.revoked_success', [], 'id'));
    }
}