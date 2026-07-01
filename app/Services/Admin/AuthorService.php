<?php

namespace App\Services\Admin;

use App\Models\Author;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AuthorService
{
    /**
     * Get all authors with book count.
     */
    public function getAll()
    {
        return Author::withCount('books')
            ->latest()
            ->get();
    }

    /**
     * Get active authors (for dropdowns).
     */
    public function getActive()
    {
        return Author::where('status', 'active')->orderBy('name')->get();
    }

    /**
     * Store a new author.
     */
    public function create(array $data): Author
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $this->uploadImage($data['image']);
        }

        $data['created_by'] = auth('staff')->id();
        $data['updated_by'] = auth('staff')->id();

        return Author::create($data);
    }

    public function store(array $data): Author
    {
        $genres = $data['genres'] ?? [];
        unset($data['genres']);

        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['image'] = $data['image']->store('authors', 'public');
        }

        $data['created_by'] = auth('staff')->id();
        $data['updated_by'] = auth('staff')->id();

        $author = Author::create($data);

        if (!empty($genres)) {
            $author->genres()->sync($genres);
        }

        return $author;
    }

    public function update(Author $author, array $data): Author
    {
        $genres = $data['genres'] ?? [];
        unset($data['genres']);

        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            if ($author->image && $author->image !== 'default.png') {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($author->image);
            }
            $data['image'] = $data['image']->store('authors', 'public');
        }

        $data['updated_by'] = auth('staff')->id();
        $author->update($data);

        if (!empty($genres)) {
            $author->genres()->sync($genres);
        }

        return $author;
    }

    /**
     * Delete an author.
     */
    public function delete(Author $author): void
    {
        if ($author->image) {
            Storage::disk('public')->delete($author->image);
        }

        $author->delete();
    }

    /**
     * Upload author image.
     */
    private function uploadImage(UploadedFile $file): string
    {
        return $file->store('authors', 'public');
    }
}