<?php

namespace App\Services\Customer;

use App\Models\Wishlist;

class WishlistService
{
    /**
     * Toggle wishlist: add if not exists, remove if exists.
     * Returns the new state (true = added, false = removed).
     */
    public function toggle(int $customerId, int $bookId): bool
    {
        $exists = Wishlist::where('customer_id', $customerId)
            ->where('book_id', $bookId)
            ->first();

        if ($exists) {
            $exists->delete();
            return false;
        }

        Wishlist::create([
            'customer_id' => $customerId,
            'book_id' => $bookId,
        ]);

        return true;
    }

    /**
     * Check if a book is wishlisted.
     */
    public function isWishlisted(int $customerId, int $bookId): bool
    {
        return Wishlist::where('customer_id', $customerId)
            ->where('book_id', $bookId)
            ->exists();
    }

    /**
     * Get all wishlisted books for a customer.
     */
    public function getWishlist(int $customerId)
    {
        return Wishlist::with('book.authors')
            ->where('customer_id', $customerId)
            ->latest()
            ->get();
    }

    /**
     * Get wishlisted book IDs for a customer (for heart icon check).
     */
    public function getWishlistedIds(int $customerId): array
    {
        return Wishlist::where('customer_id', $customerId)
            ->pluck('book_id')
            ->toArray();
    }
}