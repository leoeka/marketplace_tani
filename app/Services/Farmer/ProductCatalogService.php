<?php

namespace App\Services\Farmer;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductCatalogService
{
    /** @param array<string, mixed> $data */
    public function create(User $farmer, array $data, string $action): Product
    {
        $photos = $data['photos'];
        unset($data['photos'], $data['action']);

        $product = $farmer->products()->create([
            ...$data,
            'slug' => $this->uniqueSlug($data['name']),
            'status' => $action === 'submit' ? 'pending' : 'draft',
        ]);

        $this->storeImages($product, $photos);

        return $product;
    }

    /** @param array<string, mixed> $data */
    public function update(Product $product, array $data, string $action): void
    {
        $photos = $data['photos'] ?? [];
        unset($data['photos'], $data['action']);
        $wasApproved = $product->status === 'approved';

        $product->update([
            ...$data,
            'slug' => $this->uniqueSlug($data['name'], $product),
            'status' => $wasApproved || $action === 'submit' ? 'pending' : 'draft',
            'rejection_note' => null,
        ]);

        if ($photos !== []) {
            $previousImages = $product->images()->get();
            $paths = collect($photos)->map(fn (UploadedFile $photo): string => $photo->store('products/'.$product->id, 'public'));

            Storage::disk('public')->delete($previousImages->pluck('path')->all());
            $product->images()->delete();

            foreach ($paths as $sortOrder => $path) {
                $product->images()->create([
                    'path' => $path,
                    'sort_order' => $sortOrder,
                ]);
            }
        }
    }

    public function delete(Product $product): bool
    {
        return DB::transaction(function () use ($product): bool {
            $lockedProduct = Product::query()->lockForUpdate()->findOrFail($product->id);

            if ($lockedProduct->orderItems()->exists()) {
                return false;
            }

            $imagePaths = $lockedProduct->images()->pluck('path')->all();
            $lockedProduct->delete();
            Storage::disk('public')->delete($imagePaths);

            return true;
        });
    }

    /** @param array<int, UploadedFile> $photos */
    private function storeImages(Product $product, array $photos): void
    {
        foreach ($photos as $sortOrder => $photo) {
            $product->images()->create([
                'path' => $photo->store('products/'.$product->id, 'public'),
                'sort_order' => $sortOrder,
            ]);
        }
    }

    private function uniqueSlug(string $name, ?Product $product = null): string
    {
        $baseSlug = Str::slug($name) ?: 'produk';
        $slug = $baseSlug;
        $suffix = 2;

        while (Product::query()
            ->where('slug', $slug)
            ->when($product !== null, fn ($query) => $query->where('id', '!=', $product->getKey()))
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        return $slug;
    }
}