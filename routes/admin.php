<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\AiController;
use App\Http\Controllers\Admin\AiAssistantController;
use App\Http\Controllers\Admin\ReviewController;

Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('guest:staff')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::middleware('auth:staff')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Banners — requires can_manage_books
        Route::middleware('permission:can_manage_books')->group(function () {
            Route::resource('banners', BannerController::class)->except(['show']);
            Route::resource('categories', CategoryController::class)->except(['show']);
            Route::resource('authors', AuthorController::class)->except(['show']);
            Route::resource('books', BookController::class)->except(['show']);
        });

        // Customers — requires can_manage_users
        Route::middleware('permission:can_manage_users')->group(function () {
            Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
            Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
            Route::patch('customers/{customer}/status', [CustomerController::class, 'updateStatus'])->name('customers.status');
        });

        // Orders — requires can_manage_orders
        Route::middleware('permission:can_manage_orders')->group(function () {
            Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
            Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
        });

        // Payments — requires can_view_reports
        Route::middleware('permission:can_view_reports')->group(function () {
            Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        });

        // Staff & Roles — requires can_manage_users
        Route::middleware('permission:can_manage_users')->group(function () {
            Route::resource('staff', StaffController::class)->except(['show']);
            Route::resource('roles', RoleController::class)->except(['show']);
        });

        // Promotions
        Route::get('promotions', [PromotionController::class, 'index'])->name('promotions.index');
        Route::post('promotions/send', [PromotionController::class, 'send'])->name('promotions.send');

        // AI Tools — Super Admin only
        Route::middleware('permission:can_manage_users')->group(function () {
            Route::post('/ai/generate-description', [AiController::class, 'generateDescription'])->name('ai.generate-description');
            Route::post('/ai/bulk-create', [AiController::class, 'bulkCreate'])->name('ai.bulk-create');
            Route::post('/ai-assistant/chat', [AiAssistantController::class, 'chat'])->name('ai-assistant.chat');
            Route::post('/ai/ask', [AiAssistantController::class, 'chat'])->name('ai.ask');
        });

        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/reviews/{rating}/status', [ReviewController::class, 'updateStatus'])->name('reviews.status');
        Route::delete('/reviews/{rating}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

});