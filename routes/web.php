<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFarmerController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminMarketPriceController;
use App\Http\Controllers\Admin\AdminArticleController;
use App\Http\Controllers\Admin\AdminShippingServiceController;
use App\Http\Controllers\Admin\AdminCertificateController;
use App\Http\Controllers\Export\ExportController;
use App\Http\Controllers\Farmer\FarmerDashboardController;
use App\Http\Controllers\Farmer\IncomingOrderController;
use App\Http\Controllers\Farmer\FarmerProductController;
use App\Http\Controllers\Farmer\FarmerProfileController;
use App\Http\Controllers\Marketplace\MarketplaceController;
use App\Http\Controllers\Marketplace\CartController;
use App\Http\Controllers\Marketplace\CheckoutController;
use App\Http\Controllers\Marketplace\CustomerOrderController;
use App\Http\Controllers\Marketplace\MarketPriceController;
use App\Http\Controllers\Marketplace\ArticleController;
use App\Http\Middleware\SetExportLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketplaceController::class, 'home'])->name('home');
Route::prefix('export')
    ->middleware(SetExportLocale::class)
    ->name('export.')
    ->group(function (): void {
        Route::get('/', [ExportController::class, 'home'])->name('home');
        Route::get('/products', [ExportController::class, 'index'])->name('products.index');
        Route::get('/products/{slug}', [ExportController::class, 'showProduct'])->name('products.show');
        Route::get('/certifications', [ExportController::class, 'certifications'])->name('certifications.index');
    });
Route::get('/produk', [MarketplaceController::class, 'index'])->name('marketplace.products.index');
Route::get('/pra-order', [MarketplaceController::class, 'preorder'])->name('marketplace.preorders.index');
Route::get('/produk/{slug}', [MarketplaceController::class, 'showProduct'])->name('marketplace.products.show');
Route::get('/petani/{slug}', [MarketplaceController::class, 'showFarmer'])->name('marketplace.farmers.show');
Route::get('/harga-pasar', [MarketPriceController::class, 'index'])->name('marketplace.market-prices.index');
Route::get('/edukasi', [ArticleController::class, 'index'])->name('marketplace.articles.index');
Route::get('/edukasi/{slug}', [ArticleController::class, 'show'])->name('marketplace.articles.show');
Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/keranjang/{productId}', [CartController::class, 'store'])->whereNumber('productId')->name('cart.items.store');
Route::put('/keranjang/{productId}', [CartController::class, 'update'])->whereNumber('productId')->name('cart.items.update');
Route::delete('/keranjang/{productId}', [CartController::class, 'destroy'])->whereNumber('productId')->name('cart.items.destroy');

Route::middleware(['auth', 'role:customer'])->group(function (): void {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('marketplace.checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('marketplace.checkout.store');
    Route::get('/akun/pesanan', [CustomerOrderController::class, 'index'])->name('customer.orders.index');
    Route::get('/akun/pesanan/{order}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
});

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
        Route::put('/produk/{product}/data-ekspor', [AdminProductController::class, 'saveExportDetails'])->name('products.export-details.update');
        Route::post('/produk/{product}/setujui', [AdminProductController::class, 'approve'])->name('products.approve');
        Route::post('/produk/{product}/kanal', [AdminProductController::class, 'updateChannels'])->name('products.channels.update');
        Route::post('/produk/{product}/tolak', [AdminProductController::class, 'reject'])->name('products.reject');
        Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/pesanan/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/pesanan/{order}/tandai-dibayar', [AdminOrderController::class, 'markPaid'])->name('orders.mark-paid');
        Route::resource('sertifikat', AdminCertificateController::class)
            ->parameters(['sertifikat' => 'certificate'])
            ->except(['show'])
            ->names('certificates');
        Route::resource('shipping-services', AdminShippingServiceController::class)->except(['show']);
        Route::resource('harga-pasar', AdminMarketPriceController::class)
            ->parameters(['harga-pasar' => 'marketPrice'])
            ->except(['show'])
            ->names('market-prices');
        Route::resource('artikel', AdminArticleController::class)
            ->parameters(['artikel' => 'article'])
            ->except(['show'])
            ->names('articles');
    });

Route::prefix('dashboard-petani')
    ->middleware(['auth', 'role:farmer'])
    ->group(function (): void {
        Route::get('/', FarmerDashboardController::class)->name('farmer.dashboard');
        Route::get('/pesanan', [IncomingOrderController::class, 'index'])->name('farmer.orders.index');
        Route::get('/pesanan/{order}', [IncomingOrderController::class, 'show'])->name('farmer.orders.show');
        Route::post('/pesanan/{order}/resi', [IncomingOrderController::class, 'saveShipment'])->name('farmer.orders.shipments.store');
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
