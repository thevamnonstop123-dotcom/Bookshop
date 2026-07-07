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
    public function __construct(
        protected BookService $bookService,
        protected RatingService $ratingService
    ) {}

    public function index(Request $request)
    {
        // Fixed: Explicitly white-listed the 'availability' attribute array key 
        $filters = $request->only([
            'search', 'category', 'author', 'min_price', 'max_price',
            'rating', 'language', 'in_stock', 'on_sale', 'sort', 'availability'
        ]);

        $books = $this->bookService->getBooks($filters);
        $categories = $this->bookService->getCategories();
        $authors = $this->bookService->getAuthors();
        $wishlistedIds = $this->getWishlistedIds();

        if ($search = $request->get('search')) {
            $recent = session()->get('recent_searches', []);
            $recent = array_filter($recent, fn($s) => $s !== $search);
            array_unshift($recent, $search);
            session()->put('recent_searches', array_slice($recent, 0, 5));
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html'           => view('customer.books.partials.books-grid', compact('books', 'wishlistedIds'))->render(),
                'count'          => $books->total(),
                'hasPages'       => $books->hasPages(),
                'pagination'     => $books->hasPages() ? $books->appends($request->query())->links('vendor.pagination.default')->render() : '',
                'filters'        => $filters,
                'filterGroups'   => $this->bookService->getFilterGroups($filters),
                'activeFilters'  => $this->bookService->getActiveFilters($filters, $categories, $authors),
                // Fixed: Explicitly return refreshed parameters to structural data layer 
                'categories'     => $categories,
                'authors'        => $authors
            ]);
        }

        return view('customer.books.index', [
            'books'         => $books,
            'categories'    => $categories,
            'authors'       => $authors,
            'filters'       => $filters,
            'filterGroups'  => $this->bookService->getFilterGroups($filters),
            'activeFilters' => $this->bookService->getActiveFilters($filters, $categories, $authors),
            'sortOptions'   => $this->bookService->getSortOptions(),
            'priceRange'    => $this->bookService->getPriceRange(),
            'languages'     => $this->bookService->getLanguages(),
            'wishlistedIds' => $wishlistedIds,
        ]);
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

    public function clearSearchHistory() { session()->forget('recent_searches'); return back(); }

    public function deleteSearchHistory($index)
    {
        $recent = session()->get('recent_searches', []);
        if (isset($recent[$index])) unset($recent[$index]);
        session()->put('recent_searches', array_values($recent));
        return back();
    }

    private function getWishlistedIds(): array
    {
        if (!auth('customer')->check()) return [];
        return app(WishlistService::class)->getWishlistedIds(auth('customer')->id());
    }
}