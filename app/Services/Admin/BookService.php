<?php

namespace App\Services\Admin;

use App\Models\Book;
use App\Services\NotificationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BookService
{
    /**
     * Get all books with relationships.
     */
    public function getAll(array $filters = [])
    {
        return Book::with(['category', 'authors'])
            ->when(!empty($filters["availability"]), fn($q) => $q->whereIn("availability_status", explode(",", $filters["availability"])))
            ->latest()
            ->paginate(20);  // Changed from ->get() to ->paginate(20)
    }
    /**
     * Store a new book.
     */
    public function create(array $data): Book
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $this->uploadImage($data['image']);
        }

        // Handle ebook file upload (no $book exists yet, just store)
        if (isset($data['ebook_file']) && $data['ebook_file'] instanceof UploadedFile) {
            $data['ebook_file'] = $data['ebook_file']->store('ebooks', 'public');
        }

        $data['created_by'] = auth('staff')->id();
        $data['updated_by'] = auth('staff')->id();

        $authorIds = $data['author_ids'] ?? [];
        unset($data['author_ids']);

        $book = Book::create($data);

        if (!empty($authorIds)) {
            $book->authors()->sync($authorIds);
        }

        return $book->load('authors');
    }

    /**
     * Update a book.
     */
    public function update(Book $book, array $data): Book
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($book->image) {
                Storage::disk('public')->delete($book->image);
            }
            $data['image'] = $this->uploadImage($data['image']);
        }

        if (isset($data['ebook_file']) && $data['ebook_file'] instanceof UploadedFile) {
            if ($book->ebook_file ?? false) {
                Storage::disk('public')->delete($book->ebook_file);
            }
            $data['ebook_file'] = $data['ebook_file']->store('ebooks', 'public');
        }

        $data['updated_by'] = auth('staff')->id();

        // Separate author_ids
        $authorIds = $data['author_ids'] ?? [];
        unset($data['author_ids']);

        $book->update($data);

        if ($book->wasChanged("stock_quantity")) {
            $this->checkStockAlert($book);
        }

        // Sync authors
        if (!empty($authorIds)) {
            $book->authors()->sync($authorIds);
        }

        return $book->load('authors');
    }

    /**
     * Delete a book.
     */
    public function delete(Book $book): void
    {
        if ($book->image) {
            Storage::disk('public')->delete($book->image);
        }

        // Detach authors from pivot table
        $book->authors()->detach();

        $book->delete();
    }

    /**
     * Upload book cover image.
     */
    private function uploadImage(UploadedFile $file): string
    {
        return $file->store('books', 'public');
    }

    private function checkStockAlert($book): void
    {
        if ($book->stock_quantity <= 0) {
            NotificationService::send(NotificationService::bookRoles(), "out_of_stock", '"' . $book->title . '" is out of stock', "Stock reached 0. Please restock.", $book);
        } elseif ($book->stock_quantity <= 5) {
            NotificationService::send(NotificationService::bookRoles(), "low_stock", '"' . $book->title . '" is low in stock', "Only {$book->stock_quantity} left.", $book);
        }
    }
}
