<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;
use App\Models\Banner;

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
        // Share small layout data to avoid inline DB calls in blade
        View::composer('layouts.customer', function ($view) {
            $view->with('layoutCategories', Category::where('status', 'active')->limit(5)->get());
        });

        // Share site banners for pages that need them (used by home)
        View::share('siteBanners', Banner::where('status','active')
            ->whereDate('start_date','<=', now())
            ->whereDate('end_date','>=', now())
            ->orderBy('display_order')->get());
    }
}
