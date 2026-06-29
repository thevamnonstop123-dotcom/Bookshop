<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Customer\RatingService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected RatingService $ratingService;

    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'rating', 'book_id', 'search']);
        $reviews = $this->ratingService->getAdminReviews($filters);
        $stats = $this->ratingService->getAdminStats();

        return view('admin.reviews.index', compact('reviews', 'stats', 'filters'));
    }

    public function updateStatus(Request $request, $ratingId)
    {
        $request->validate(['status' => 'required|in:active,hidden,reported']);
        $this->ratingService->updateStatus($ratingId, $request->status);

        return back()->with('success', 'Review status updated.');
    }

    public function destroy($ratingId)
    {
        $rating = \App\Models\Rating::findOrFail($ratingId);
        $bookId = $rating->book_id;
        $rating->delete();
        $this->ratingService->recalculateBookRating($bookId);

        return back()->with('success', 'Review deleted permanently.');
    }
}