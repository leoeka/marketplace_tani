<?php

namespace App\Providers;

use App\Models\Rfq;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('components.dashboard-layout', function (View $view): void {
            if (Auth::user()?->role !== 'admin') {
                return;
            }

            $newRfqCount = Rfq::query()->status('new')->count();
            $menu = collect(config('dashboard.admin_menu'))
                ->map(function (array $item) use ($newRfqCount): array {
                    if ($item['label'] === 'rfqs') {
                        $item['count'] = $newRfqCount;
                    }

                    return $item;
                })
                ->all();

            $view->with('menu', $menu);
        });
    }
}
