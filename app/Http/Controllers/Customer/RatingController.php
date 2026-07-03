<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Services\Customer\RatingService;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    protected RatingService $ratingService;

    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    /**
     * Store or update a rating.
     */
    public function store(Request $request, $bookId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ]);

        $customerId = auth('customer')->id();

        // Check purchase
        if (!$this->ratingService->hasPurchased($customerId, $bookId)) {
            return response()->json([
                'message' => 'You can only review books you have purchased.',
            ], 403);
        }

        $rating = $this->ratingService->rate(
            $customerId,
            $bookId,
            $request->rating,
            $request->review
        );
        NotificationService::send(NotificationService::reviewRoles(), "new_review", "New Review on " . $rating->book->title, $rating->customer->name . " left a " . $rating->rating . "-star review", $rating);

        return response()->json([
            'message' => 'Review submitted successfully!',
            'rating' => $rating->load('customer'),
            'book_rating' => $rating->book->fresh()->only(['rating', 'rating_count']),
        ]);
    }

    /**
     * Delete a rating.
     */
    public function destroy($ratingId)
    {
        $this->ratingService->deleteRating($ratingId, auth('customer')->id());

        return response()->json(['message' => 'Review deleted.']);
    }

    /**
     * Toggle helpful.
     */
    public function toggleHelpful($ratingId)
    {
        $result = $this->ratingService->toggleHelpful($ratingId, auth('customer')->id());

        return response()->json($result);
    }

    /**
     * Get reviews for a book (AJAX).
     */
    public function bookReviews(Request $request, $bookId)
    {
        $sort = $request->get('sort', 'newest');
        $reviews = $this->ratingService->getBookReviews($bookId, $sort);

        return response()->json($reviews);
    }
}