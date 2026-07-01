<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AuthorRequest;
use App\Models\Author;
use App\Services\Admin\AuthorService;

class AuthorController extends Controller
{
    protected AuthorService $authorService;

    public function __construct(AuthorService $authorService)
    {
        $this->authorService = $authorService;
    }

    /**
     * Display all authors.
     */
    public function index()
    {
        $authors = $this->authorService->getAll();

        return view('admin.authors.index', compact('authors'));
    }

    /**
     * Show create form.
     */
   public function create()
    {
        $countries = \App\Models\Country::orderBy('name')->get();
        $genres = \App\Models\Genre::orderBy('name')->get();
        return view('admin.authors.create', compact('countries', 'genres'));
    }

    /**
     * Store a new author.
     */
    public function store(AuthorRequest $request)
    {
        $this->authorService->create($request->validated());

        return redirect()
            ->route('admin.authors.index')
            ->with('success', 'Author created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Author $author)
    {
        $countries = \App\Models\Country::orderBy('name')->get();
        $genres = \App\Models\Genre::orderBy('name')->get();
        return view('admin.authors.edit', compact('author', 'countries', 'genres'));
    }

    /**
     * Update an author.
     */
    public function update(AuthorRequest $request, Author $author)
    {
        $this->authorService->update($author, $request->validated());

        return redirect()
            ->route('admin.authors.index')
            ->with('success', 'Author updated successfully.');
    }

    /**
     * Delete an author.
     */
    public function destroy(Author $author)
    {
        $this->authorService->delete($author);

        return redirect()
            ->route('admin.authors.index')
            ->with('success', 'Author deleted successfully.');
    }
}