<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;

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
        // Share categories ONLY for customer layout (good — specific)
        View::composer('layouts.customer', function ($view) {
            $view->with('layoutCategories', Category::where('status', 'active')->limit(5)->get());
        });
    }
}