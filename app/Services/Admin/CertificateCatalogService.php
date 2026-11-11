<?php

namespace App\Services\Admin;

use App\Models\Certificate;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CertificateCatalogService
{
    /** @param array<string, mixed> $data */
    public function save(?Certificate $certificate, array $data): Certificate
    {
        $file = $data['file'] ?? null;
        $productIds = $data['product_ids'] ?? [];
        $previousFilePath = $certificate?->file_path;

        unset($data['file'], $data['product_ids']);

        if ($file instanceof UploadedFile) {
            $data['file_path'] = $file->store('certificates', 'public');
        }

        if ($certificate === null) {
            $certificate = Certificate::query()->create($data);
        } else {
            $certificate->update($data);
        }

        $certificate->products()->sync($productIds);

        if ($previousFilePath !== null && isset($data['file_path'])) {
            Storage::disk('public')->delete($previousFilePath);
        }

        return $certificate;
    }

    public function delete(Certificate $certificate): void
    {
        $filePath = $certificate->file_path;
        $certificate->delete();

        if ($filePath !== null) {
            Storage::disk('public')->delete($filePath);
        }
    }
}