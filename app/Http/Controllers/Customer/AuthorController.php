<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\AuthorService;
use App\Services\Customer\WishlistService;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    protected AuthorService $authorService;

    public function __construct(AuthorService $authorService)
    {
        $this->authorService = $authorService;
    }

    /**
     * Display author detail with their books.
     */
    public function show(int $authorId, Request $request)
    {
        $author = $this->authorService->getAuthor($authorId);
        $filters = $request->only(['sort']);
        $books = $this->authorService->getAuthorBooks($authorId, $filters);

        $wishlistedIds = [];
        if (auth('customer')->check()) {
            $wishlistedIds = app(WishlistService::class)
                ->getWishlistedIds(auth('customer')->id());
        }

        return view('customer.authors.show', compact('author', 'books', 'filters', 'wishlistedIds'));
    }

    /**
     * Display all authors listing.
     */
    public function index()
    {
        $authors = $this->authorService->getAuthors();

        return view('customer.authors.index', compact('authors'));
    }
}