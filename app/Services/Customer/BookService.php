<?php

namespace App\Services\Customer;

use App\Models\Book;
use App\Models\Category;
use App\Models\Author;

class BookService
{
    public function getBooks(array $filters = [])
    {
        return Book::with(['authors', 'category'])
            ->where('status', 'active')
            ->when(!empty($filters['search']), function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    $q->where('title', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                      ->orWhereHas('authors', fn($a) => $a->where('name', 'like', '%' . $filters['search'] . '%'))
                      ->orWhereHas('category', fn($c) => $c->where('name', 'like', '%' . $filters['search'] . '%'));
                });
            })
            ->when(!empty($filters['category']), fn($q) => $q->where('category_id', $filters['category']))
            ->when(!empty($filters['author']), fn($q) => $q->whereHas('authors', fn($a) => $a->where('authors.id', $filters['author'])))
            ->when(!empty($filters['min_price']), fn($q) => $q->where('price', '>=', $filters['min_price']))
            ->when(!empty($filters['max_price']), fn($q) => $q->where('price', '<=', $filters['max_price']))
            ->when(!empty($filters['rating']), fn($q) => $q->where('rating', '>=', $filters['rating']))
            ->when(!empty($filters['language']), fn($q) => $q->where('language', $filters['language']))
            ->when(!empty($filters['in_stock']), fn($q) => $q->where('stock_quantity', '>', 0))
            ->when(!empty($filters['on_sale']), function ($q) {
                $q->whereNotNull('sale_price')->where('sale_price', '<', \DB::raw('price'))
                  ->where(fn($q) => $q->whereNull('sale_ends_at')->orWhere('sale_ends_at', '>=', now()));
            })
            ->when(!empty($filters['sort']), function ($query) use ($filters) {
                match ($filters['sort']) {
                    'price_asc'  => $query->orderBy('price', 'asc'),
                    'price_desc' => $query->orderBy('price', 'desc'),
                    'bestseller' => $query->withCount('orderItems')->orderByDesc('order_items_count'),
                    'rated'      => $query->orderByDesc('rating'),
                    'a_z'        => $query->orderBy('title', 'asc'),
                    'z_a'        => $query->orderBy('title', 'desc'),
                    'newest'     => $query->latest(),
                    'oldest'     => $query->oldest(),
                    default      => $query->latest(),
                };
            }, fn($q) => $q->latest())
            ->paginate(20);
    }

    public function getCategories()
    {
        return Category::where('status', 'active')
            ->withCount(['books' => fn($q) => $q->where('status', 'active')])
            ->orderBy('name')->get();
    }

    public function getAuthors()
    {
        return Author::where('status', 'active')
            ->whereHas('books', fn($q) => $q->where('status', 'active'))
            ->withCount(['books' => fn($q) => $q->where('status', 'active')])
            ->orderBy('name')->get();
    }

    public function getPriceRange(): array
    {
        $stats = Book::where('status', 'active')
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();
        return ['min' => (int)($stats->min_price ?? 0), 'max' => (int)($stats->max_price ?? 100000)];
    }

    public function getLanguages(): array
    {
        return Book::where('status', 'active')->whereNotNull('language')
            ->where('language', '!=', '')->distinct()->pluck('language')->toArray();
    }

    public function getFilterGroups(array $filters = []): array
    {
        return [
            ['key' => 'category', 'label' => 'Category', 'isActive' => !empty($filters['category'])],
            ['key' => 'author',   'label' => 'Author',   'isActive' => !empty($filters['author'])],
            ['key' => 'price',    'label' => 'Price',    'isActive' => !empty($filters['min_price']) || !empty($filters['max_price'])],
            ['key' => 'rating',   'label' => 'Rating',   'isActive' => !empty($filters['rating'])],
        ];
    }

    public function getSortOptions(): array
    {
        return [
            'featured'    => 'Featured',
            'bestseller'  => 'Best Selling',
            'newest'      => 'Newest',
            'rated'       => 'Highest Rated',
            'price_asc'   => 'Price: Low → High',
            'price_desc'  => 'Price: High → Low',
            'a_z'         => 'A–Z',
        ];
    }

    public function getActiveFilters(array $filters, $categories, $authors): array
    {
        $active = [];
        if (!empty($filters['search'])) {
            $active[] = ['param' => 'search', 'label' => '"' . $filters['search'] . '"'];
        }
        if (!empty($filters['category'])) {
            $cat = $categories->firstWhere('id', $filters['category']);
            $active[] = ['param' => 'category', 'label' => $cat->name ?? 'Category'];
        }
        if (!empty($filters['author'])) {
            $author = $authors->firstWhere('id', $filters['author']);
            $active[] = ['param' => 'author', 'label' => $author->name ?? 'Author'];
        }
        if (!empty($filters['rating'])) {
            $active[] = ['param' => 'rating', 'label' => $filters['rating'] . '+ Stars'];
        }
        if (!empty($filters['min_price']) || !empty($filters['max_price'])) {
            $min = !empty($filters['min_price']) ? number_format($filters['min_price']) : '0';
            $max = !empty($filters['max_price']) ? number_format($filters['max_price']) : 'Any';
            $active[] = ['param' => 'price', 'label' => $min . ' — ' . $max . ' MMK'];
        }
        if (!empty($filters['language'])) {
            $active[] = ['param' => 'language', 'label' => $filters['language']];
        }
        if (!empty($filters['in_stock'])) {
            $active[] = ['param' => 'in_stock', 'label' => 'In Stock'];
        }
        if (!empty($filters['on_sale'])) {
            $active[] = ['param' => 'on_sale', 'label' => 'On Sale'];
        }
        return $active;
    }
}
