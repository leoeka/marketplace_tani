<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\UpdateFarmerProfileRequest;
use App\Services\Farmer\FarmerProfileService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FarmerProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('farmer.profile.edit', [
            'menu' => config('dashboard.farmer_menu'),
            'title' => __('farmer.profile.title', [], 'id'),
            'profile' => $request->user()->farmerProfile,
        ]);
    }

    public function update(UpdateFarmerProfileRequest $request, FarmerProfileService $profileService): RedirectResponse
    {
        $profileService->save($request->user(), $request->validated());

        return redirect()->route('farmer.profile.edit')->with('success', __('farmer.profile.saved', [], 'id'));
    }
}