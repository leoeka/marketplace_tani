<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('/dashboard', function (Request $request) {
    return redirect()->route($request->user()->dashboardRouteName());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('/admin', 'admin.dashboard')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.dashboard');

Route::view('/dashboard-petani', 'farmer.dashboard')
    ->middleware(['auth', 'role:farmer'])
    ->name('farmer.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
