<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Rating;
use App\Services\Customer\BookService;
use App\Services\Customer\RatingService;
use App\Services\Customer\WishlistService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected BookService $bookService;
    protected RatingService $ratingService;

    public function __construct(BookService $bookService, RatingService $ratingService)
    {
        $this->bookService = $bookService;
        $this->ratingService = $ratingService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category', 'min_price', 'max_price', 'author', 'rating', 'sort']);
        $books = $this->bookService->getBooks($filters);
        $categories = $this->bookService->getCategories();
        $authors = $this->bookService->getAuthors();
        $priceRange = $this->bookService->getPriceRange();
        $wishlistedIds = $this->getWishlistedIds();

        // Save recent searches to session
        if ($search = $request->get('search')) {
            $recent = session()->get('recent_searches', []);
            $recent = array_filter($recent, fn($s) => $s !== $search); // Remove duplicate
            array_unshift($recent, $search); // Add to front
            $recent = array_slice($recent, 0, 5); // Keep last 5
            session()->put('recent_searches', $recent);
        }

        return view('customer.books.index', compact('books', 'categories', 'authors', 'filters', 'priceRange', 'wishlistedIds'));
    }

    public function clearSearchHistory()
    {
        session()->forget('recent_searches');
        return back();
    }

    public function deleteSearchHistory($index)
    {
        $recent = session()->get('recent_searches', []);
        if (isset($recent[$index])) {
            unset($recent[$index]);
        }
        session()->put('recent_searches', array_values($recent));

        return back();
    }

    public function show($slug)
    {
        $book = Book::with(['authors', 'category'])->where('slug', $slug)->where('status', 'active')->firstOrFail();

        $relatedBooks = Book::with('authors')->where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)->where('status', 'active')->limit(4)->get();

        $authorBooks = Book::with('authors')->where('status', 'active')
            ->whereHas('authors', fn($q) => $q->whereIn('authors.id', $book->authors->pluck('id')))
            ->where('id', '!=', $book->id)->limit(6)->get();

        $wishlistedIds = $this->getWishlistedIds();

        $customerId = auth('customer')->id() ?? null;
        $existingRating = null;
        $hasPurchased = false;
        $reviews = collect();

        if ($customerId) {
            $existingRating = Rating::where('customer_id', $customerId)->where('book_id', $book->id)->first();
            $hasPurchased = $this->ratingService->hasPurchased($customerId, $book->id);
        }

        if ($book->rating_count > 0) {
            $reviews = $this->ratingService->getBookReviews($book->id, 'newest', 5);
        }

        // Feature highlights
        $highlights = [
            ['icon' => 'fa-truck-fast', 'label' => 'Free Delivery', 'desc' => 'On orders above 50,000 MMK'],
            ['icon' => 'fa-tablet-screen-button', 'label' => 'Instant eBook', 'desc' => 'Read immediately after purchase'],
            ['icon' => 'fa-shield-halved', 'label' => 'Secure Payment', 'desc' => 'Encrypted checkout'],
            ['icon' => 'fa-rotate-left', 'label' => 'Easy Returns', 'desc' => '7-day return policy'],
            ['icon' => 'fa-star', 'label' => 'Verified Reviews', 'desc' => 'From real customers'],
        ];

        return view('customer.books.show', compact(
            'book', 'relatedBooks', 'authorBooks', 'wishlistedIds',
            'existingRating', 'hasPurchased', 'reviews', 'highlights'
        ));
    }

    private function getWishlistedIds(): array
    {
        if (!auth('customer')->check()) return [];
        return app(WishlistService::class)->getWishlistedIds(auth('customer')->id());
    }
}