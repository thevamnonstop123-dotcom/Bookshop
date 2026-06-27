<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Book;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Services\Customer\WishlistService;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('status','active')
            ->whereDate('start_date','<=', now())
            ->whereDate('end_date','>=', now())
            ->orderBy('display_order')
            ->get();

        $totalBooks = Book::where('status','active')->count();
        $totalCustomers = Customer::count();
        $totalOrders = Order::count();

        $categories = Category::where('status','active')->get();

        $newBooks = Book::with(['authors','category'])->where('status','active')->latest()->limit(5)->get();
        $bestSellers = Book::with(['authors','category'])->where('status','active')->inRandomOrder()->limit(5)->get();

        $topAuthors = \App\Models\Author::withCount('books')
            ->where('status', 'active')
            ->orderByDesc('books_count')
            ->limit(5)
            ->get();

        // Get the most famous book for each author
        $topAuthors->each(function ($author) {
            $author->famousBook = $author->books()->where('status', 'active')->first();
        });

        // Wishlisted book IDs for heart icon
        $wishlistedIds = [];
        if (auth('customer')->check()) {
            $wishlistedIds = app(WishlistService::class)
                ->getWishlistedIds(auth('customer')->id());
        }

            return view('customer.home', compact(
            'banners',
            'totalBooks',
            'totalCustomers',
            'totalOrders',
            'categories',
            'newBooks',
            'bestSellers',
            'wishlistedIds',
            'topAuthors'
        ));
    }
}