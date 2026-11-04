<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFarmerController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Farmer\FarmerDashboardController;
use App\Http\Controllers\Farmer\FarmerProductController;
use App\Http\Controllers\Farmer\FarmerProfileController;
use App\Http\Controllers\Marketplace\MarketplaceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketplaceController::class, 'home'])->name('home');
Route::get('/produk', [MarketplaceController::class, 'index'])->name('marketplace.products.index');
Route::get('/produk/{slug}', [MarketplaceController::class, 'showProduct'])->name('marketplace.products.show');
Route::get('/petani/{slug}', [MarketplaceController::class, 'showFarmer'])->name('marketplace.farmers.show');

Route::get('/dashboard', function (Request $request) {
    return redirect()->route($request->user()->dashboardRouteName());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function (): void {
        Route::redirect('/', '/admin/dashboard')->name('home');
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::get('/petani', [AdminFarmerController::class, 'index'])->name('farmers.index');
        Route::get('/petani/{farmerProfile}', [AdminFarmerController::class, 'show'])->name('farmers.show');
        Route::post('/petani/{farmerProfile}/verifikasi', [AdminFarmerController::class, 'verify'])->name('farmers.verify');
        Route::delete('/petani/{farmerProfile}/verifikasi', [AdminFarmerController::class, 'revoke'])->name('farmers.revoke');
        Route::get('/produk', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('/produk/{product}', [AdminProductController::class, 'show'])->name('products.show');
        Route::post('/produk/{product}/setujui', [AdminProductController::class, 'approve'])->name('products.approve');
        Route::post('/produk/{product}/kanal', [AdminProductController::class, 'updateChannels'])->name('products.channels.update');
        Route::post('/produk/{product}/tolak', [AdminProductController::class, 'reject'])->name('products.reject');
    });

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
