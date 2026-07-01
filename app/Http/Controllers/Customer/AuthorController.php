<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\AuthorService;
use App\Services\Customer\WishlistService;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function __construct(
        protected AuthorService $authorService,
        protected WishlistService $wishlistService
    ) {}

    /**
     * Show author profile + books
     */
    public function show(int $authorId, Request $request)
    {
        // Normalize filters (future-proof)
        $filters = $request->only([
            'sort',
            'genre',
            'price_min',
            'price_max'
        ]);

        $author = $this->authorService->getAuthor($authorId, [
            'genres',
            'books'
        ]);

        $books = $this->authorService->getAuthorBooks($authorId, $filters);

        $wishlistedIds = $this->getWishlistIds();

        // AJAX response (partial render)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view(
                    'customer.authors.partials.books-grid',
                    compact('author', 'books', 'filters', 'wishlistedIds')
                )->render(),

                'pagination' => $books->appends($filters)->links()->render(),
                'hasPages' => $books->hasPages(),
                'booksCount' => $books->total(),
                'currentSort' => $filters['sort'] ?? 'latest',
            ]);
        }

        return view('customer.authors.show', compact(
            'author',
            'books',
            'filters',
            'wishlistedIds'
        ));
    }

    /**
     * List all authors
     */
    public function index()
    {
        $authors = $this->authorService->getAuthors([
            'with' => ['genres']
        ]);

        return view('customer.authors.index', compact('authors'));
    }

    /**
     * Centralized wishlist access
     */
    private function getWishlistIds(): array
    {
        if (!auth('customer')->check()) {
            return [];
        }

        return $this->wishlistService
            ->getWishlistedIds(auth('customer')->id());
    }
}