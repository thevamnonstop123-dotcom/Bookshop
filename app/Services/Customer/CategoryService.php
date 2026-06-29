<?php

namespace App\Services\Customer;

use App\Models\Category;
use App\Models\Book;

class CategoryService
{
    /**
     * Get category by ID with book count.
     */
    public function getCategory(int $categoryId): Category
    {
        return Category::withCount('books')
            ->where('status', 'active')
            ->findOrFail($categoryId);
    }

    /**
     * Get all active categories with book counts.
     */
    public function getCategories()
    {
        return Category::withCount('books')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get filtered books for a category.
     */
    public function getCategoryBooks(int $categoryId, array $filters = [])
    {
        return Book::with(['authors', 'category'])
            ->where('status', 'active')
            ->where('category_id', $categoryId)
            ->when(isset($filters['min_price']), function ($query) use ($filters) {
                $query->where('price', '>=', $filters['min_price']);
            })
            ->when(isset($filters['max_price']), function ($query) use ($filters) {
                $query->where('price', '<=', $filters['max_price']);
            })
            ->when(isset($filters['author']), function ($query) use ($filters) {
                $query->whereHas('authors', function ($q) use ($filters) {
                    $q->where('authors.id', $filters['author']);
                });
            })
            ->when(isset($filters['rating']), function ($query) use ($filters) {
                $query->where('rating', '>=', $filters['rating']);
            })
            ->when(isset($filters['sort']), function ($query) use ($filters) {
                match ($filters['sort']) {
                    'bestseller' => $query->withCount('orderItems')->orderByDesc('order_items_count'),
                    'rated'      => $query->orderByDesc('rating'),
                    'price_asc'  => $query->orderBy('price', 'asc'),
                    'price_desc' => $query->orderBy('price', 'desc'),
                    'popular'    => $query->orderByDesc('views'),
                    'latest'     => $query->latest(),
                    default      => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            })
            ->paginate(20);
    }

    /**
     * Get authors that have books in this category.
     */
    public function getCategoryAuthors(int $categoryId, int $limit = 8)
    {
        return \App\Models\Author::where('status', 'active')
            ->whereHas('books', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId)->where('status', 'active');
            })
            ->withCount(['books' => function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            }])
            ->orderByDesc('books_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get price range for a category.
     */
    public function getPriceRange(int $categoryId): array
    {
        $stats = Book::where('category_id', $categoryId)
            ->where('status', 'active')
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return [
            'min' => (int) ($stats->min_price ?? 0),
            'max' => (int) ($stats->max_price ?? 100000),
        ];
    }
}