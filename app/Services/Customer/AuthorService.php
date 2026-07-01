<?php

namespace App\Services\Customer;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;

class AuthorService
{
    /**
     * Base author query (single source of truth)
     */
    private function baseAuthorQuery(): Builder
    {
        return Author::query()
            ->where('status', 'active');
    }

    /**
     * Get author by ID with relations
     */
    public function getAuthor(int $authorId): Author
    {
        return $this->baseAuthorQuery()
            ->withCount('books')
            ->with(['genres'])
            ->findOrFail($authorId);
    }

    /**
     * Get author's books with filters
     */
    public function getAuthorBooks(int $authorId, array $filters = [])
    {
        $query = Book::query()
            ->with(['authors', 'category'])
            ->where('status', 'active')
            ->whereHas('authors', function ($q) use ($authorId) {
                $q->where('authors.id', $authorId);
            });

        // ----------------------------
        // PRICE FILTER
        // ----------------------------
        if (!empty($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        // ----------------------------
        // SORTING (clean switch, not match)
        // ----------------------------
        $sort = $filters['sort'] ?? 'latest';

        switch ($sort) {

            case 'bestseller':
                $query->withCount('orderItems')
                      ->orderByDesc('order_items_count');
                break;

            case 'rated':
                $query->orderByDesc('rating');
                break;

            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;

            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;

            case 'latest':
            default:
                $query->latest();
                break;
        }

        return $query->paginate(12);
    }

    /**
     * Get all active authors
     */
    public function getAuthors(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->baseAuthorQuery()
            ->withCount('books')
            ->orderBy('name')
            ->get();
    }

    /**
     * Top authors by books count
     */
    public function getTopAuthors(int $limit = 8)
    {
        return $this->baseAuthorQuery()
            ->withCount('books')
            ->orderByDesc('books_count')
            ->limit($limit)
            ->get();
    }
}