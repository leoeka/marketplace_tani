<?php

namespace App\Services\Farmer;

use App\Models\FarmerProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FarmerProfileService
{
    /** @param array<string, mixed> $data */
    public function save(User $farmer, array $data): FarmerProfile
    {
        $profile = $farmer->farmerProfile;
        $oldPhoto = $profile?->photo;
        $photo = $data['photo'] ?? null;
        unset($data['photo']);

        if ($photo instanceof UploadedFile) {
            $data['photo'] = $photo->store('farmer-profiles/'.$farmer->id, 'public');
        }

        $data['slug'] = $this->uniqueSlug($data['shop_name'], $profile);

        if ($profile === null) {
            $profile = FarmerProfile::create([
                ...$data,
                'user_id' => $farmer->id,
            ]);
        } else {
            $profile->update($data);
        }

        if ($oldPhoto !== null && isset($data['photo'])) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return $profile;
    }

    private function uniqueSlug(string $shopName, ?FarmerProfile $profile): string
    {
        $baseSlug = Str::slug($shopName) ?: 'toko-petani';
        $slug = $baseSlug;
        $suffix = 2;

        while (FarmerProfile::query()
            ->where('slug', $slug)
            ->when($profile !== null, fn ($query) => $query->where('id', '!=', $profile->getKey()))
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        return $slug;
    }
}