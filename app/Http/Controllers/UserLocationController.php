<?php

namespace App\Http\Controllers;

use App\Models\UserLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserLocationController extends Controller
{
    /** Menyimpan koordinat. Hanya untuk pengguna login yang sudah mengizinkan. */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
        ]);

        if ($user->location_consent !== 'granted') {
            return response()->json(['message' => 'Izin lokasi belum diberikan.'], 403);
        }

        // Satu catatan per pengguna per jam, supaya tabel tidak membengkak.
        $recent = UserLocation::where('user_id', $user->id)
            ->where('captured_at', '>=', now()->subHour())
            ->exists();

        if (! $recent) {
            UserLocation::create([
                'user_id' => $user->id,
                'latitude' => round((float) $data['latitude'], 6),
                'longitude' => round((float) $data['longitude'], 6),
                'accuracy_m' => isset($data['accuracy']) ? (int) round($data['accuracy']) : null,
                'captured_at' => now(),
            ]);
        }

        return response()->json(['ok' => true]);
    }

    /** Mencatat pilihan pengguna: granted atau denied. */
    public function consent(Request $request): JsonResponse
    {
        $data = $request->validate(['choice' => ['required', 'in:granted,denied']]);

        $request->user()->forceFill([
            'location_consent' => $data['choice'],
            'location_consent_at' => now(),
        ])->save();

        return response()->json(['ok' => true]);
    }

    /** Pengguna menarik izin: semua koordinat tersimpan dihapus. */
    public function destroy(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $user->locations()->delete();
        $user->forceFill(['location_consent' => 'denied', 'location_consent_at' => now()])->save();

        return back()->with('success', 'Izin lokasi ditarik dan data lokasi Anda dihapus.');
    }
}