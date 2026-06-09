<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookRequest;
use App\Models\Book;
use App\Services\Admin\BookService;
use App\Services\Admin\CategoryService;
use App\Services\Admin\AuthorService;

class BookController extends Controller
{
    protected BookService $bookService;
    protected CategoryService $categoryService;
    protected AuthorService $authorService;

    public function __construct(
        BookService $bookService,
        CategoryService $categoryService,
        AuthorService $authorService
    ) {
        $this->bookService = $bookService;
        $this->categoryService = $categoryService;
        $this->authorService = $authorService;
    }

    /**
     * Display all books.
     */
    public function index()
    {
        $books = $this->bookService->getAll();

        return view('admin.books.index', compact('books'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $categories = $this->categoryService->getActive();
        $authors = $this->authorService->getActive();

        return view('admin.books.create', compact('categories', 'authors'));
    }

    /**
     * Store a new book.
     */
    public function store(BookRequest $request)
    {
        $this->bookService->create($request->validated());

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Book created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Book $book)
    {
        $categories = $this->categoryService->getActive();
        $authors = $this->authorService->getActive();
        $selectedAuthors = $book->authors->pluck('id')->toArray();

        return view('admin.books.edit', compact('book', 'categories', 'authors', 'selectedAuthors'));
    }

    /**
     * Update a book.
     */
    public function update(BookRequest $request, Book $book)
    {
        $this->bookService->update($book, $request->validated());

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Book updated successfully.');
    }

    /**
     * Delete a book.
     */
    public function destroy(Book $book)
    {
        $this->bookService->delete($book);

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Book deleted successfully.');
    }
}