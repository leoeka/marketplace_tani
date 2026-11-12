<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveLoadingPortRequest;
use App\Models\LoadingPort;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;

class AdminLoadingPortController extends Controller
{
    public function index(): View
    {
        return view('admin.logistics.ports.index', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.logistics.ports.title', [], 'id'),
            'loadingPorts' => LoadingPort::query()->orderBy('sort_order')->orderBy('name')->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.logistics.ports.form', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.logistics.ports.create_title', [], 'id'),
            'loadingPort' => new LoadingPort(['is_active' => true, 'sort_order' => 0]),
            'action' => route('admin.loading-ports.store'),
            'method' => 'POST',
        ]);
    }

    public function store(SaveLoadingPortRequest $request): RedirectResponse
    {
        LoadingPort::query()->create($request->validated());

        return redirect()->route('admin.loading-ports.index')
            ->with('success', __('admin.logistics.ports.created', [], 'id'));
    }

    public function edit(LoadingPort $loadingPort): View
    {
        return view('admin.logistics.ports.form', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.logistics.ports.edit_title', [], 'id'),
            'loadingPort' => $loadingPort,
            'action' => route('admin.loading-ports.update', $loadingPort),
            'method' => 'PUT',
        ]);
    }

    public function update(SaveLoadingPortRequest $request, LoadingPort $loadingPort): RedirectResponse
    {
        $loadingPort->update($request->validated());

        return redirect()->route('admin.loading-ports.index')
            ->with('success', __('admin.logistics.ports.updated', [], 'id'));
    }

    public function destroy(LoadingPort $loadingPort): RedirectResponse
    {
        if ($loadingPort->freightEstimates()->exists()) {
            return redirect()->route('admin.loading-ports.index')
                ->with('error', __('admin.logistics.ports.in_use', [], 'id'));
        }

        try {
            $loadingPort->delete();
        } catch (QueryException) {
            return redirect()->route('admin.loading-ports.index')
                ->with('error', __('admin.logistics.ports.in_use', [], 'id'));
        }

        return redirect()->route('admin.loading-ports.index')
            ->with('success', __('admin.logistics.ports.deleted', [], 'id'));
    }
}