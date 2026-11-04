<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Farmer\FarmerDashboardController;
use App\Http\Controllers\Farmer\FarmerProductController;
use App\Http\Controllers\Farmer\FarmerProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('/dashboard', function (Request $request) {
    return redirect()->route($request->user()->dashboardRouteName());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('/admin', 'admin.dashboard')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.dashboard');

Route::prefix('dashboard-petani')
    ->middleware(['auth', 'role:farmer'])
    ->group(function (): void {
        Route::get('/', FarmerDashboardController::class)->name('farmer.dashboard');
        Route::resource('produk', FarmerProductController::class)
            ->parameters(['produk' => 'product'])
            ->except(['show'])
            ->names([
                'index' => 'farmer.products.index',
                'create' => 'farmer.products.create',
                'store' => 'farmer.products.store',
                'edit' => 'farmer.products.edit',
                'update' => 'farmer.products.update',
                'destroy' => 'farmer.products.destroy',
            ]);
        Route::get('/profil-toko', [FarmerProfileController::class, 'edit'])->name('farmer.profile.edit');
        Route::put('/profil-toko', [FarmerProfileController::class, 'update'])->name('farmer.profile.update');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
