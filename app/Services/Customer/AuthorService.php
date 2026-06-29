<?php

namespace App\Services\Customer;

use App\Models\Author;
use App\Models\Book;

class AuthorService
{
    /**
     * Get author by ID with relationships.
     */
    public function getAuthor(int $authorId): Author
    {
        return Author::withCount('books')
            ->where('status', 'active')
            ->findOrFail($authorId);
    }

    /**
     * Get author's books with sorting.
     */
    public function getAuthorBooks(int $authorId, array $filters = [])
    {
        return Book::with(['authors', 'category'])
            ->where('status', 'active')
            ->whereHas('authors', function ($query) use ($authorId) {
                $query->where('authors.id', $authorId);
            })
            ->when(isset($filters['sort']), function ($query) use ($filters) {
                match ($filters['sort']) {
                    'bestseller' => $query->withCount('orderItems')->orderByDesc('order_items_count'),
                    'rated'      => $query->orderByDesc('rating'),
                    'price_asc'  => $query->orderBy('price', 'asc'),
                    'price_desc' => $query->orderBy('price', 'desc'),
                    'latest'     => $query->latest(),
                    default      => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            })
            ->paginate(12);
    }

    /**
     * Get all active authors for listing.
     */
    public function getAuthors()
    {
        return Author::withCount('books')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get top authors by book count.
     */
    public function getTopAuthors(int $limit = 8)
    {
        return Author::withCount('books')
            ->where('status', 'active')
            ->orderByDesc('books_count')
            ->limit($limit)
            ->get();
    }
}