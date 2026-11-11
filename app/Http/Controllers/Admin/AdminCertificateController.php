<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCertificateRequest;
use App\Models\Certificate;
use App\Models\Product;
use App\Services\Admin\CertificateCatalogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AdminCertificateController extends Controller
{
    public function index(): View
    {
        return view('admin.certificates.index', [
            'menu' => config('dashboard.admin_menu'),
            'title' => __('admin.certificates.title', [], 'id'),
            'certificates' => Certificate::query()
                ->withCount('products')
                ->latest()
                ->orderByDesc('id')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return $this->formView(new Certificate(), __('admin.certificates.create_title', [], 'id'));
    }

    public function store(SaveCertificateRequest $request, CertificateCatalogService $certificates): RedirectResponse
    {
        $certificates->save(null, $request->validated());

        return redirect()->route('admin.certificates.index')
            ->with('success', __('admin.certificates.created', [], 'id'));
    }

    public function edit(Certificate $certificate): View
    {
        return $this->formView(
            $certificate->load('products'),
            __('admin.certificates.edit_title', [], 'id'),
        );
    }

    public function update(
        SaveCertificateRequest $request,
        Certificate $certificate,
        CertificateCatalogService $certificates,
    ): RedirectResponse {
        $certificates->save($certificate, $request->validated());

        return redirect()->route('admin.certificates.index')
            ->with('success', __('admin.certificates.updated', [], 'id'));
    }

    public function destroy(Certificate $certificate, CertificateCatalogService $certificates): RedirectResponse
    {
        $certificates->delete($certificate);

        return redirect()->route('admin.certificates.index')
            ->with('success', __('admin.certificates.deleted', [], 'id'));
    }

    private function formView(Certificate $certificate, string $title): View
    {
        return view('admin.certificates.form', [
            'menu' => config('dashboard.admin_menu'),
            'title' => $title,
            'certificate' => $certificate,
            'products' => Product::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}