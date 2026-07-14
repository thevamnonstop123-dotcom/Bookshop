<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\EbookAccess;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EbookController extends Controller
{
    /**
     * Show customer's purchased e-books.
     */
    public function library()
    {
        $customerId = auth('customer')->id();
        
        $purchasedBooks = Book::where('status', 'active')
            ->whereHas('orderItems', function ($q) use ($customerId) {
                $q->where('format', 'ebook')
                  ->whereHas('order', function ($sub) use ($customerId) {
                      $sub->where('customer_id', $customerId)
                         ->where('status', '!=', 'cancelled');
                  });
            })
            ->with('authors')
            ->get();

        return view('customer.ebooks.library', compact('purchasedBooks'));
    }

    /**
     * Read e-book (inline PDF viewer).
     */
    public function read($bookId)
    {
        $customerId = auth('customer')->id();
        
        // Verify purchase - check for ebook format order item
        $purchased = Book::where('id', $bookId)
            ->where('status', 'active')
            ->whereHas('orderItems', function ($q) use ($customerId) {
                $q->where('format', 'ebook')
                  ->whereHas('order', function ($sub) use ($customerId) {
                      $sub->where('customer_id', $customerId)
                         ->where('status', '!=', 'cancelled');
                  });
            })
            ->exists();

        if (!$purchased) {
            abort(403, 'You have not purchased this e-book.');
        }

        // Track access
        EbookAccess::updateOrCreate(
            ['customer_id' => $customerId, 'book_id' => $bookId],
            ['last_accessed_at' => now(), 'device_token' => session()->getId()]
        );

        $book = Book::findOrFail($bookId);
        $filePath = $book->ebook_file;

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'E-book file not found.');
        }

        return response()->file(Storage::disk('public')->path($filePath), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $book->title . '.pdf"',
        ]);
    }

    /**
     * Download e-book.
     */
    public function download($bookId)
    {
        $customerId = auth('customer')->id();
        
        $purchased = Book::where('id', $bookId)
            ->where('status', 'active')
            ->whereHas('orderItems', function ($q) use ($customerId) {
                $q->where('format', 'ebook')
                  ->whereHas('order', function ($sub) use ($customerId) {
                      $sub->where('customer_id', $customerId)
                         ->where('status', '!=', 'cancelled');
                  });
            })
            ->exists();

        if (!$purchased) {
            abort(403, 'You have not purchased this e-book.');
        }

        $book = Book::findOrFail($bookId);

        if (!$book->ebook_file || !Storage::disk('public')->exists($book->ebook_file)) {
            abort(404);
        }

        return Storage::disk('public')->download($book->ebook_file, $book->title . '.pdf');
    }
}
