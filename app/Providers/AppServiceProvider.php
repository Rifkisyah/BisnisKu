<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            static $shopName = null;
            if ($shopName === null) {
                try {
                    $setting = \Illuminate\Support\Facades\DB::table('business_settings')->first();
                    $shopName = ($setting && !empty($setting->shop_name)) ? $setting->shop_name : 'BisnisKu';
                } catch (\Exception $e) {
                    $shopName = 'BisnisKu';
                }
            }
            $view->with('globalShopName', $shopName);
        });
        \Illuminate\Database\Eloquent\Builder::macro('applySort', function (string|null $sort, string $defaultCol = 'created_at') {
            return match($sort) {
                'created_at_asc'  => $this->oldest(),
                'updated_at_desc' => $this->orderBy('updated_at', 'desc'),
                'updated_at_asc'  => $this->orderBy('updated_at', 'asc'),
                default           => $this->latest($defaultCol),
            };
        });
    }
}
