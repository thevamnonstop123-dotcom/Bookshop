<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\AuthController;
use App\Http\Controllers\Customer\BookController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\ForgotPasswordController;
use App\Http\Controllers\Customer\EbookController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/

// ========== PUBLIC ROUTES (No auth required) ==========
Route::get('/', [HomeController::class, 'index'])->name('customer.home');
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{slug}', [BookController::class, 'show'])->name('books.show');

// ========== GUEST ROUTES (Only for non-logged-in users) ==========
Route::middleware('guest:customer')->group(function () {
    // Login & Register
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Password Reset (Using your custom ForgotPasswordController)
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

// ========== SOCIAL LOGIN (Guest only) ==========
Route::middleware('guest:customer')->group(function () {
    Route::get('/login/google', [App\Http\Controllers\Customer\SocialiteController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/login/google/callback', [App\Http\Controllers\Customer\SocialiteController::class, 'handleGoogleCallback']);
    Route::get('/login/facebook', [App\Http\Controllers\Customer\SocialiteController::class, 'redirectToFacebook'])->name('login.facebook');
    Route::get('/login/facebook/callback', [App\Http\Controllers\Customer\SocialiteController::class, 'handleFacebookCallback']);
});

// ========== AUTHENTICATED ROUTES (Logged-in users only) ==========
Route::middleware('auth:customer')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Cart
    Route::post('/cart/add', [App\Http\Controllers\Customer\CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{cartItem}', [App\Http\Controllers\Customer\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cartItem}', [App\Http\Controllers\Customer\CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/data', [App\Http\Controllers\Customer\CartController::class, 'getData'])->name('cart.data');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('customer.orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('customer.orders.show');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('customer.profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('customer.profile.update');
    Route::post('/profile/address', [ProfileController::class, 'storeAddress'])->name('customer.address.store');
    Route::put('/profile/address/{addressId}', [ProfileController::class, 'updateAddress'])->name('customer.address.update');
    Route::delete('/profile/address/{addressId}', [ProfileController::class, 'deleteAddress'])->name('customer.address.delete');
    Route::patch('/profile/address/{addressId}/default', [ProfileController::class, 'setDefaultAddress'])->name('customer.address.default');

    // E-Books
    Route::get('/my-library', [EbookController::class, 'library'])->name('customer.ebooks.library');
    Route::get('/ebooks/{book}/read', [EbookController::class, 'read'])->name('customer.ebooks.read');
    Route::get('/ebooks/{book}/download', [EbookController::class, 'download'])->name('customer.ebooks.download');
});