<?php

namespace App\Services\Customer;

use App\Models\Book;
use App\Models\Category;
use App\Models\Author;

class BookService
{
    /**
     * Get filtered, sorted books.
     */
    public function getBooks(array $filters = [])
    {
        return Book::with(['authors', 'category'])
            ->where('status', 'active')
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhereHas('authors', function ($a) use ($filters) {
                        $a->where('name', 'like', '%' . $filters['search'] . '%');
                    })
                    ->orWhereHas('category', function ($c) use ($filters) {
                        $c->where('name', 'like', '%' . $filters['search'] . '%');
                    });
                });
            })
            ->when(isset($filters['category']), function ($query) use ($filters) {
                $query->where('category_id', $filters['category']);
            })
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
                    'price_asc'   => $query->orderBy('price', 'asc'),
                    'price_desc'  => $query->orderBy('price', 'desc'),
                    'bestseller'  => $query->withCount('orderItems')->orderByDesc('order_items_count'),
                    'rated'       => $query->orderByDesc('rating'),
                    'latest'      => $query->latest(),
                    default       => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            })
            ->paginate(20);
    }

    /**
     * Get all active categories.
     */
    public function getCategories()
    {
        return Category::where('status', 'active')->orderBy('name')->get();
    }

    /**
     * Get all active authors (for filter).
     */
    public function getAuthors()
    {
        return Author::where('status', 'active')
            ->whereHas('books', function ($q) {
                $q->where('status', 'active');
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Get price range for all active books.
     */
    public function getPriceRange(): array
    {
        $stats = Book::where('status', 'active')
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return [
            'min' => (int) ($stats->min_price ?? 0),
            'max' => (int) ($stats->max_price ?? 100000),
        ];
    }
}