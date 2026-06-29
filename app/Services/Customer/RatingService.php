<?php

namespace App\Services\Customer;

use App\Models\Rating;
use App\Models\RatingHelpful;
use App\Models\Book;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

class RatingService
{
    /**
     * Check if customer purchased the book.
     */
    public function hasPurchased(int $customerId, int $bookId): bool
    {
        return Order::where('customer_id', $customerId)
            ->where('status', 'delivered')
            ->whereHas('items', function ($query) use ($bookId) {
                $query->where('book_id', $bookId);
            })
            ->exists();
    }

    /**
     * Create or update a rating.
     */
    public function rate(int $customerId, int $bookId, int $rating, ?string $review = null): Rating
    {
        $ratingModel = Rating::updateOrCreate(
            ['customer_id' => $customerId, 'book_id' => $bookId],
            [
                'rating' => $rating,
                'review' => $review,
                'status' => 'active',
            ]
        );

        $this->recalculateBookRating($bookId);

        return $ratingModel;
    }

    /**
     * Get customer's rating for a book.
     */
    public function getUserRating(int $customerId, int $bookId): ?Rating
    {
        return Rating::where('customer_id', $customerId)
            ->where('book_id', $bookId)
            ->first();
    }

    /**
     * Get paginated reviews for a book.
     */
    public function getBookReviews(int $bookId, string $sort = 'newest', int $perPage = 10): LengthAwarePaginator
    {
        return Rating::with(['customer'])
            ->where('book_id', $bookId)
            ->where('status', 'active')
            ->when($sort === 'helpful', fn($q) => $q->orderByDesc('helpful_count'))
            ->when($sort === 'highest', fn($q) => $q->orderByDesc('rating'))
            ->when($sort === 'lowest', fn($q) => $q->orderBy('rating'))
            ->when($sort === 'newest' || !$sort, fn($q) => $q->latest())
            ->paginate($perPage);
    }

    /**
     * Toggle helpful vote.
     */
    public function toggleHelpful(int $ratingId, int $customerId): array
    {
        $rating = Rating::findOrFail($ratingId);
        $existing = RatingHelpful::where('rating_id', $ratingId)
            ->where('customer_id', $customerId)
            ->first();

        if ($existing) {
            $existing->delete();
            $rating->decrement('helpful_count');
            $action = 'removed';
        } else {
            RatingHelpful::create(['rating_id' => $ratingId, 'customer_id' => $customerId]);
            $rating->increment('helpful_count');
            $action = 'added';
        }

        return [
            'action' => $action,
            'count' => $rating->fresh()->helpful_count,
        ];
    }

    /**
     * Delete a rating (customer's own).
     */
    public function deleteRating(int $ratingId, int $customerId): void
    {
        $rating = Rating::where('id', $ratingId)
            ->where('customer_id', $customerId)
            ->firstOrFail();

        $bookId = $rating->book_id;
        $rating->delete();
        $this->recalculateBookRating($bookId);
    }

    /**
     * Get all reviews by a customer.
     */
    public function getCustomerReviews(int $customerId, int $perPage = 10): LengthAwarePaginator
    {
        return Rating::with('book')
            ->where('customer_id', $customerId)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Recalculate book average rating.
     */
    public function recalculateBookRating(int $bookId): void
    {
        $stats = Rating::where('book_id', $bookId)
            ->where('status', 'active')
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
            ->first();

        Book::where('id', $bookId)->update([
            'rating' => round($stats->avg_rating ?? 0, 1),
            'rating_count' => $stats->total ?? 0,
        ]);
    }

    // ========== ADMIN METHODS ==========

    public function getAdminStats(): array
    {
        return [
            'total' => Rating::count(),
            'average' => round(Rating::avg('rating') ?? 0, 1),
            'hidden' => Rating::where('status', 'hidden')->count(),
            'reported' => Rating::where('status', 'reported')->count(),
        ];
    }

    public function getAdminReviews(array $filters = [])
    {
        return Rating::with(['customer', 'book'])
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['rating']), fn($q) => $q->where('rating', $filters['rating']))
            ->when(isset($filters['book_id']), fn($q) => $q->where('book_id', $filters['book_id']))
            ->when(isset($filters['search']), function ($q) use ($filters) {
                $q->where('review', 'like', '%' . $filters['search'] . '%');
            })
            ->latest()
            ->paginate(20);
    }

    public function updateStatus(int $ratingId, string $status): void
    {
        $rating = Rating::findOrFail($ratingId);
        $rating->update(['status' => $status]);
        $this->recalculateBookRating($rating->book_id);
    }
}