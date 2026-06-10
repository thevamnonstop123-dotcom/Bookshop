<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\BookService;
use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    protected BookService $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * Display book listing with filters.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category', 'min_price', 'max_price', 'sort']);
        $books = $this->bookService->getBooks($filters);
        $categories = $this->bookService->getCategories();

        return view('customer.books.index', compact('books', 'categories', 'filters'));
    }

    /**
     * Display book detail.
     */
    public function show($slug)
    {
        $book = \App\Models\Book::with(['authors', 'category'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $relatedBooks = \App\Models\Book::with('authors')
            ->where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->where('status', 'active')
            ->limit(4)
            ->get();

        return view('customer.books.show', compact('book', 'relatedBooks'));
    }
}