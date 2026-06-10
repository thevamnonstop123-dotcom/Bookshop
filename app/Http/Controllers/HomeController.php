<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Book;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show home page with preloaded data to avoid inline queries in blade.
     */
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

        return view('customer.home', compact(
            'banners',
            'totalBooks',
            'totalCustomers',
            'totalOrders',
            'categories',
            'newBooks',
            'bestSellers'
        ));
    }
}
