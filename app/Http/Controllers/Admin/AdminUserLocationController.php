<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;

class AdminUserLocationController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with('latestLocation')
            ->orderByRaw("case when location_consent = 'granted' then 0 else 1 end")
            ->orderBy('name')
            ->paginate(20);

        // PENTING: ganti dua baris di bawah dengan cara AdminFarmerController::index
        // menyusun $menu dan $title (salin persis, lalu tambahkan menu 'locations').
        $menu = [];
        $title = __('dashboard.menu.locations');

        return view('admin.locations.index', compact('users', 'menu', 'title'));
    }
}