<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveFreightEstimateRequest;
use App\Models\FreightEstimate;
use App\Models\LoadingPort;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminFreightEstimateController extends Controller
{
    public function index(Request $request): View
    {
        $mode = in_array($request->query('mode'), ['sea', 'air'], true) ? $request->query('mode') : null;

        return view('admin.logistics.estimates.index', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.logistics.estimates.title', [], 'id'),
            'freightEstimates' => FreightEstimate::query()
                ->with('loadingPort')
                ->when($mode, fn ($query, string $selectedMode) => $query->where('mode', $selectedMode))
                ->orderBy('destination')
                ->orderBy('mode')
                ->paginate(12)
                ->withQueryString(),
            'selectedMode' => $mode,
        ]);
    }

    public function create(): View
    {
        return view('admin.logistics.estimates.form', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.logistics.estimates.create_title', [], 'id'),
            'freightEstimate' => new FreightEstimate(['mode' => 'sea', 'is_active' => true]),
            'loadingPorts' => LoadingPort::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'action' => route('admin.freight-estimates.store'),
            'method' => 'POST',
        ]);
    }

    public function store(SaveFreightEstimateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['loading_port_id'] = $data['mode'] === 'air' ? null : $data['loading_port_id'];

        FreightEstimate::query()->create($data);

        return redirect()->route('admin.freight-estimates.index')
            ->with('success', __('admin.logistics.estimates.created', [], 'id'));
    }

    public function edit(FreightEstimate $freightEstimate): View
    {
        return view('admin.logistics.estimates.form', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.logistics.estimates.edit_title', [], 'id'),
            'freightEstimate' => $freightEstimate,
            'loadingPorts' => LoadingPort::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'action' => route('admin.freight-estimates.update', $freightEstimate),
            'method' => 'PUT',
        ]);
    }

    public function update(SaveFreightEstimateRequest $request, FreightEstimate $freightEstimate): RedirectResponse
    {
        $data = $request->validated();
        $data['loading_port_id'] = $data['mode'] === 'air' ? null : $data['loading_port_id'];
        $freightEstimate->update($data);

        return redirect()->route('admin.freight-estimates.index')
            ->with('success', __('admin.logistics.estimates.updated', [], 'id'));
    }

    public function destroy(FreightEstimate $freightEstimate): RedirectResponse
    {
        $freightEstimate->delete();

        return redirect()->route('admin.freight-estimates.index')
            ->with('success', __('admin.logistics.estimates.deleted', [], 'id'));
    }
}