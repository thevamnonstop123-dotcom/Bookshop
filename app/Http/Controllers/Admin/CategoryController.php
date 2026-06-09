<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Services\Admin\CategoryService;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display all categories.
     */
    public function index()
    {
        $categories = $this->categoryService->getAll();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a new category.
     */
    public function store(CategoryRequest $request)
    {
        $this->categoryService->create($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update a category.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $this->categoryService->update($category, $request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Delete a category.
     */
    public function destroy(Category $category)
    {
        $this->categoryService->delete($category);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}