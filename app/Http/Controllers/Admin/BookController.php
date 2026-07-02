<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookRequest;
use App\Models\Book;
use Illuminate\Http\Request;
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
    public function index(Request $request)
    {
        $filters = $request->only(["availability"]);
        $books = $this->bookService->getAll($filters);

        return view('admin.books.index', compact('books'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $categories = $this->categoryService->getActive();
        $authors = $this->authorService->getActive();
        $availabilityOptions = ["in_stock" => "In Stock", "low_stock" => "Low Stock", "out_of_stock" => "Out of Stock", "coming_soon" => "Coming Soon", "pre_order" => "Pre-order", "discontinued" => "Discontinued"];

        return view('admin.books.create', compact('categories', 'authors', 'availabilityOptions'));
    }

    /**
     * Store a new book.
     */
    public function store(BookRequest $request)
    {
        $bookIds = is_array($request->book_ids) ? $request->book_ids : explode(",", $request->book_ids);
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
        $availabilityOptions = ["in_stock" => "In Stock", "low_stock" => "Low Stock", "out_of_stock" => "Out of Stock", "coming_soon" => "Coming Soon", "pre_order" => "Pre-order", "discontinued" => "Discontinued"];
        $selectedAuthors = $book->authors->pluck('id')->toArray();

        return view('admin.books.edit', compact('availabilityOptions', 'book', 'categories', 'authors', 'selectedAuthors'));
    }

    /**
     * Update a book.
     */
    public function update(BookRequest $request, Book $book)
    {
        $bookIds = is_array($request->book_ids) ? $request->book_ids : explode(",", $request->book_ids);
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
    public function bulkUpdate(Request $request)
    {
        $bookIds = is_array($request->book_ids) ? $request->book_ids : explode(",", $request->book_ids);
        $request->validate([
            'book_ids' => 'required',
            'availability_status' => 'required|string|in:in_stock,low_stock,out_of_stock,coming_soon,pre_order,discontinued',
        ]);

        Book::whereIn("id", $bookIds)->update([
            'availability_status' => $request->availability_status,
        ]);

        return back()->with('success', 'Inventory updated for ' . count($bookIds) . ' books.');
    }
}