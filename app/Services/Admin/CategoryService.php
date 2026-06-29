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

    public function store(array $data): Category
    {
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['image'] = $data['image']->store('categories', 'public');
        }

        $data['created_by'] = auth('staff')->id();
        $data['updated_by'] = auth('staff')->id();

        return Category::create($data);
    }

    /**
     * Update a category.
     */
    public function update(Category $category, array $data): Category
    {
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            if ($category->image && $category->image !== 'default.png') {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $data['image']->store('categories', 'public');
        }

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