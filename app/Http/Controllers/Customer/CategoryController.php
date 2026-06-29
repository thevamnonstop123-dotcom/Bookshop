<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CategoryService;
use App\Services\Customer\WishlistService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display category with filtered books.
     */
    public function show(int $categoryId, Request $request)
    {
        $category = $this->categoryService->getCategory($categoryId);
        $filters = $request->only(['min_price', 'max_price', 'author', 'rating', 'sort']);
        $books = $this->categoryService->getCategoryBooks($categoryId, $filters);
        $authors = $this->categoryService->getCategoryAuthors($categoryId);
        $priceRange = $this->categoryService->getPriceRange($categoryId);

        $wishlistedIds = [];
        if (auth('customer')->check()) {
            $wishlistedIds = app(WishlistService::class)
                ->getWishlistedIds(auth('customer')->id());
        }

        return view('customer.categories.show', compact(
            'category', 'books', 'authors', 'filters', 'priceRange', 'wishlistedIds'
        ));
    }
}