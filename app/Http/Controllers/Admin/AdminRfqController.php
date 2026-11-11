<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rfq;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminRfqController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $status = in_array($status, ['new', 'handled'], true) ? $status : null;
        $search = trim((string) $request->query('search', ''));
        $search = mb_substr($search, 0, 100);

        return view('admin.rfqs.index', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.rfqs.title', [], 'id'),
            'filters' => ['status' => $status, 'search' => $search],
            'rfqs' => Rfq::query()
                ->when($status, fn (Builder $query, string $filter): Builder => $query->status($filter))
                ->when($search !== '', fn ($query) => $query->where(function ($searchQuery) use ($search): void {
                    $searchQuery
                        ->where('buyer_name', 'like', '%'.$search.'%')
                        ->orWhere('company', 'like', '%'.$search.'%');
                }))
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function show(Rfq $rfq): View
    {
        return view('admin.rfqs.show', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.rfqs.detail_title', [], 'id'),
            'rfq' => $rfq,
        ]);
    }

    public function markHandled(Rfq $rfq): RedirectResponse
    {
        $rfq->update([
            'status' => 'handled',
            'handled_at' => now(),
        ]);

        return back()->with('success', __('admin.rfqs.handled_success', [], 'id'));
    }

    public function markNew(Rfq $rfq): RedirectResponse
    {
        $rfq->update([
            'status' => 'new',
            'handled_at' => null,
        ]);

        return back()->with('success', __('admin.rfqs.new_success', [], 'id'));
    }
}