<?php

namespace App\Services\Admin;

use App\Models\Category;

class CategoryService
{
    /**
     * Get all categories with book count, latest first.
     */
    public function getAll()
    {
        return Category::withCount('books')
            ->latest()
            ->get();
    }

    /**
     * Get only active categories (for dropdowns).
     */
    public function getActive()
    {
        return Category::where('status', 'active')->orderBy('name')->get();
    }

    /**
     * Store a new category.
     */
    public function create(array $data): Category
    {
        $data['created_by'] = auth('staff')->id();
        $data['updated_by'] = auth('staff')->id();

        return Category::create($data);
    }

    /**
     * Update a category.
     */
    public function update(Category $category, array $data): Category
    {
        $data['updated_by'] = auth('staff')->id();

        $category->update($data);

        return $category;
    }

    /**
     * Delete a category.
     */
    public function delete(Category $category): void
    {
        $category->delete();
    }
}