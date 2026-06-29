@extends('layouts.customer')

@section('title', $book->title . ' — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/book-detail.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/books.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/rating.css') }}">
@endpush

@section('content')

<div class="book-detail-page">

    {{-- ========== BREADCRUMB ========== --}}
    <div class="container">
        <nav class="detail-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('customer.home') }}"><i class="fas fa-home"></i> Home</a>
            <i class="fas fa-chevron-right"></i>
            <a href="{{ route('books.index') }}">Books</a>
            @if($book->category)
                <i class="fas fa-chevron-right"></i>
                <a href="{{ route('books.index', ['category' => $book->category->id]) }}">{{ $book->category->name }}</a>
            @endif
            <i class="fas fa-chevron-right"></i>
            <span>{{ Str::limit($book->title, 40) }}</span>
        </nav>
    </div>

    {{-- ========== HERO SECTION ========== --}}
    <section class="book-hero">
        <div class="container">
            <div class="book-hero-grid">

                {{-- LEFT: Book Cover --}}
                <div class="book-hero-cover">
                    <div class="book-cover-card">
                        @if($book->isOnSale())
                            <span class="book-cover-badge">-{{ $book->discountPercentage() }}%</span>
                        @endif
                        <img src="{{ $book->image && $book->image !== 'default.png' ? asset('storage/'.$book->image) : 'https://placehold.co/600x800/F1F5F9/1E3A8A?text='.urlencode($book->title) }}"
                             alt="{{ $book->title }}" class="book-cover-img">
                    </div>
                    @if($book->isEbook())
                        <div class="book-cover-format-tag">
                            <i class="fas fa-tablet-screen-button"></i> eBook Available
                        </div>
                    @endif
                </div>

                {{-- CENTER: Book Info --}}
                <div class="book-hero-info">
                    <span class="book-info-category">{{ $book->category?->name ?? 'Uncategorized' }}</span>
                    <h1 class="book-info-title">{{ $book->title }}</h1>

                    <div class="book-info-rating">
                        <span class="book-info-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star{{ $i <= round($book->rating) ? '' : '-empty' }}"></i>
                            @endfor
                        </span>
                        <span class="book-info-rating-value">{{ number_format($book->rating, 1) }}</span>
                        <span class="book-info-rating-count">({{ number_format($book->rating_count) }} {{ Str::plural('review', $book->rating_count) }})</span>
                    </div>

                    <div class="book-info-author">
                        <span>By</span>
                        @foreach($book->authors as $author)
                            <a href="{{ route('authors.show', $author) }}">{{ $author->name }}</a>{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    </div>

                    {{-- Meta Grid --}}
                    <div class="book-info-meta">
                        <div class="book-meta-item"><span class="book-meta-label">ISBN</span><span class="book-meta-value">{{ $book->isbn }}</span></div>
                        <div class="book-meta-item"><span class="book-meta-label">Language</span><span class="book-meta-value">{{ $book->language }}</span></div>
                        <div class="book-meta-item"><span class="book-meta-label">Published</span><span class="book-meta-value">{{ $book->published_date->format('M Y') }}</span></div>
                        <div class="book-meta-item"><span class="book-meta-label">Format</span><span class="book-meta-value">{{ $book->isEbook() ? 'eBook + Print' : 'Paperback' }}</span></div>
                    </div>

                    {{-- Tags --}}
                    @if($book->category)
                        <div class="book-info-tags">
                            <a href="{{ route('books.index', ['category' => $book->category->id]) }}" class="book-info-tag">{{ $book->category->name }}</a>
                            @foreach($book->authors->take(2) as $author)
                                <a href="{{ route('authors.show', $author) }}" class="book-info-tag">{{ $author->name }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- RIGHT: Purchase Card --}}
                <div class="book-hero-purchase">
                    <div class="book-purchase-card" id="purchaseCard">
                        <div class="book-purchase-price">
                            @if($book->isOnSale())
                                <span class="purchase-price-original">{{ number_format($book->price) }} MMK</span>
                                <span class="purchase-price-sale">{{ number_format($book->sale_price) }} MMK</span>
                                <span class="purchase-price-save">Save {{ $book->discountPercentage() }}%</span>
                            @else
                                <span class="purchase-price-current">{{ number_format($book->price) }} MMK</span>
                            @endif
                        </div>

                        {{-- Stock --}}
                        <div class="book-purchase-stock">
                            @if($book->isEbook())
                                <span class="purchase-stock purchase-stock-ebook"><i class="fas fa-infinity"></i> Instant access</span>
                            @elseif($book->isInStock())
                                <span class="purchase-stock purchase-stock-in"><i class="fas fa-check-circle"></i> In Stock</span>
                            @else
                                <span class="purchase-stock purchase-stock-out"><i class="fas fa-times-circle"></i> Out of Stock</span>
                            @endif
                        </div>

                        {{-- Quantity --}}
                        @if(!$book->isEbook() && $book->isInStock())
                            <div class="book-purchase-qty">
                                <button type="button" class="purchase-qty-btn" onclick="changeQuantity(-1)"><i class="fas fa-minus"></i></button>
                                <input type="number" id="quantity" class="purchase-qty-input" value="1" min="1" max="{{ $book->stock_quantity }}" readonly>
                                <button type="button" class="purchase-qty-btn" onclick="changeQuantity(1)"><i class="fas fa-plus"></i></button>
                            </div>
                        @endif

                        {{-- Buttons --}}
                        @if($book->isEbook() || $book->isInStock())
                            <button class="purchase-btn purchase-btn-cart btn-add-cart" data-book-id="{{ $book->id }}">
                                <i class="fas fa-shopping-bag"></i> Add to Cart
                            </button>
                            <button class="purchase-btn purchase-btn-buy" id="buyNowBtn">
                                <i class="fas fa-bolt"></i> Buy Now
                            </button>
                            @auth('customer')
                                <button class="purchase-btn purchase-btn-wishlist {{ in_array($book->id, $wishlistedIds) ? 'wishlisted' : '' }}"
                                        data-book-id="{{ $book->id }}" onclick="toggleWishlist(this, {{ $book->id }})">
                                    <i class="{{ in_array($book->id, $wishlistedIds) ? 'fas' : 'far' }} fa-heart"></i>
                                    <span>Wishlist</span>
                                </button>
                            @endauth
                        @else
                            <button class="purchase-btn purchase-btn-disabled" disabled>
                                <i class="fas fa-ban"></i> Out of Stock
                            </button>
                        @endif

                        {{-- Trust Badges --}}
                        <div class="book-purchase-trust">
                            <span><i class="fas fa-truck-fast"></i> Free Delivery</span>
                            <span><i class="fas fa-shield-halved"></i> Secure Checkout</span>
                            <span><i class="fas fa-rotate-left"></i> Easy Returns</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ========== FEATURE HIGHLIGHTS ========== --}}
    <section class="book-highlights">
        <div class="container">
            <div class="highlights-grid">
                @foreach($highlights as $hl)
                    <div class="highlight-card">
                        <div class="highlight-icon"><i class="fas {{ $hl['icon'] }}"></i></div>
                        <div class="highlight-info">
                            <span class="highlight-label">{{ $hl['label'] }}</span>
                            <span class="highlight-desc">{{ $hl['desc'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========== ABOUT + BOOK INFO ========== --}}
    <section class="book-about">
        <div class="container">
            <div class="book-about-grid">
                @if($book->description)
                    <div class="book-about-desc">
                        <h2 class="book-section-title">About This Book</h2>
                        <div class="book-about-text">{{ $book->description }}</div>
                    </div>
                @endif
                <div class="book-about-info">
                    <h2 class="book-section-title">Book Details</h2>
                    <div class="book-details-list">
                        <div class="book-detail-row"><span>Publisher</span><span>Bookshop Press</span></div>
                        <div class="book-detail-row"><span>ISBN</span><span>{{ $book->isbn }}</span></div>
                        <div class="book-detail-row"><span>Language</span><span>{{ $book->language }}</span></div>
                        <div class="book-detail-row"><span>Published</span><span>{{ $book->published_date->format('F d, Y') }}</span></div>
                        <div class="book-detail-row"><span>Category</span><span>{{ $book->category?->name ?? 'General' }}</span></div>
                        <div class="book-detail-row"><span>Format</span><span>{{ $book->isEbook() ? 'eBook + Paperback' : 'Paperback' }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== ABOUT THE AUTHOR ========== --}}
    @if($book->authors->isNotEmpty())
        <section class="book-author-section">
            <div class="container">
                <h2 class="book-section-title">About the Author</h2>
                @foreach($book->authors as $author)
                    <div class="book-author-card">
                        <img src="{{ $author->image && $author->image !== 'default.png' ? asset('storage/'.$author->image) : 'https://placehold.co/200x200/F1F5F9/1E3A8A?text='.urlencode(substr($author->name,0,1)) }}"
                             alt="{{ $author->name }}" class="book-author-avatar">
                        <div class="book-author-info">
                            <h3 class="book-author-name">{{ $author->name }}</h3>
                            @if($author->bio)
                                <p class="book-author-bio">{{ Str::limit($author->bio, 200) }}</p>
                            @endif
                            <a href="{{ route('authors.show', $author) }}" class="book-author-link">
                                View Profile <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ========== OTHER BOOKS BY AUTHOR ========== --}}
    @if($authorBooks->isNotEmpty())
        <section class="book-author-books">
            <div class="container">
                <h2 class="book-section-title">Other Books by {{ $book->authors->first()->name }}</h2>
                <div class="book-carousel">
                    @foreach($authorBooks as $abook)
                        @include('components.customer.book-card', ['book' => $abook])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ========== REVIEWS ========== --}}
    <section class="book-reviews" id="reviewsSection">
        <div class="container">
            <div class="book-reviews-header">
                <h2 class="book-section-title">Customer Reviews</h2>
                <div class="book-reviews-header-right">
                    @auth('customer')
                        @if($hasPurchased)
                            <button class="book-review-write-btn" onclick="openReviewModal()">
                                <i class="fas fa-star"></i> {{ $existingRating ? 'Edit Review' : 'Write Review' }}
                            </button>
                        @endif
                    @endauth
                    @guest('customer')
                        <button class="book-review-write-btn" onclick="openLoginModal()">
                            <i class="fas fa-star"></i> Write Review
                        </button>
                    @endguest
                </div>
            </div>

            <div class="book-reviews-layout">
                @if($book->rating_count > 0)
                    <div class="book-reviews-stats">
                        @include('customer.reviews.rating-stats', ['book' => $book])
                    </div>
                @endif

                <div class="book-reviews-list-wrap">
                    <div class="book-reviews-sort">
                        <span>Sort by:</span>
                        <select class="category-sort-select" name="sort" onchange="loadReviews('{{ $book->id }}', this.value)">
                            <option value="newest">Newest</option>
                            <option value="helpful">Most Helpful</option>
                            <option value="highest">Highest Rating</option>
                            <option value="lowest">Lowest Rating</option>
                        </select>
                    </div>
                    <div class="book-reviews-list" id="reviewsList">
                        @forelse($reviews as $review)
                            @include('customer.reviews.review-card', ['review' => $review])
                        @empty
                            <div class="book-reviews-empty">
                                <i class="fas fa-star-half-stroke"></i>
                                <h4>No reviews yet</h4>
                                <p>Be the first person to review this book.</p>
                            </div>
                        @endforelse
                    </div>
                    @if($reviews instanceof \Illuminate\Pagination\LengthAwarePaginator && $reviews->hasPages())
                        <div class="book-reviews-pagination">
                            {{ $reviews->appends(request()->query())->links('vendor.pagination.default') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ========== RELATED BOOKS ========== --}}
    @if($relatedBooks->isNotEmpty())
        <section class="book-related">
            <div class="container">
                <h2 class="book-section-title">You May Also Like</h2>
                <div class="book-related-grid">
                    @foreach($relatedBooks as $related)
                        @include('components.customer.book-card', ['book' => $related])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</div>

{{-- Sticky Mobile Bar --}}
<div class="book-mobile-bar" id="bookMobileBar">
    <div class="book-mobile-bar-price">
        <span>{{ number_format($book->isOnSale() ? $book->sale_price : $book->price) }} MMK</span>
    </div>
    @if($book->isEbook() || $book->isInStock())
        <button class="book-mobile-bar-cart btn-add-cart" data-book-id="{{ $book->id }}">
            <i class="fas fa-shopping-bag"></i> Add to Cart
        </button>
    @endif
</div>

{{-- Write Review Modal --}}
@auth('customer')
    @if($hasPurchased)
        <button class="floating-review-btn" onclick="openReviewModal()">
            <i class="fas fa-star"></i> Review
        </button>
        @include('customer.reviews.write-review-modal', ['book' => $book, 'existingRating' => $existingRating])
    @endif
@endauth

@endsection

@push('scripts')
    <script src="{{ asset('js/customer/book-detail.js') }}"></script>
    <script src="{{ asset('js/customer/rating.js') }}"></script>
@endpush