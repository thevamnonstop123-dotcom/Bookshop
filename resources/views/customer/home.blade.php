@extends('layouts.customer')

@section('title', 'Bookshop — Premium Books & Stationery')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/books.css') }}">
@endpush

@section('content')

    {{-- HERO BANNER SLIDER --}}
    <section class="hero" id="heroSlider">
        @if($banners->count() > 0)
            <div class="hero-track" id="heroTrack">
                @foreach($banners as $banner)
                    @php $hasImage = $banner->image && Storage::disk('public')->exists($banner->image); @endphp
                    <div class="hero-slide {{ $loop->first ? 'active' : '' }}">
                        <div class="hero-slide-bg {{ $hasImage ? '' : 'no-image' }}"
                             @if($hasImage) style="background-image: url('{{ asset('storage/'.$banner->image) }}');" @endif>
                        </div>
                        <div class="hero-overlay"></div>
                        <div class="container hero-container">
                            <div class="hero-body">
                                <span class="hero-chip">
                                    <i class="fas fa-sparkles"></i> {{ $banner->badge_text ?? 'Special Offer' }}
                                </span>
                                <h1 class="hero-heading">{{ $banner->title }}</h1>
                                <p class="hero-description">{{ $banner->description }}</p>
                                    @if($banner->end_date)
                                <div class="hero-countdown" data-end="{{ $banner->end_date->format('Y-m-d H:i:s') }}">
                                    <span class="hero-countdown-label">Offer ends in:</span>
                                    <div class="hero-countdown-timer">
                                        <span class="countdown-days-wrap">
                                            <span class="countdown-days">00</span>d
                                        </span>
                                        <span class="countdown-hours">00</span>h
                                        <span class="countdown-mins">00</span>m
                                        <span class="countdown-secs">00</span>s
                                    </div>
                                </div>
                                @endif
                                <div class="hero-actions">
                                    <a href="{{ route('books.index') }}" class="btn-hero btn-hero-primary">
                                        Shop Now <i class="fas fa-arrow-right"></i>
                                    </a>
                                    <a href="{{ route('books.index', ['sort' => 'latest']) }}" class="btn-hero btn-hero-ghost">
                                        New Arrivals
                                    </a>
                                </div>
                            </div>
                            @if($hasImage)
                                <div class="hero-media">
                                    <div class="hero-image-wrapper">
                                        <img src="{{ asset('storage/'.$banner->image) }}" alt="{{ $banner->title }}" class="hero-image">
                                        <div class="hero-image-glow"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if($banners->count() > 1)
                <button class="hero-arrow hero-arrow-left" id="heroPrev" aria-label="Previous slide">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="hero-arrow hero-arrow-right" id="heroNext" aria-label="Next slide">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="hero-indicators" id="heroIndicators">
                    @foreach($banners as $i => $b)
                        <button class="hero-dot {{ $loop->first ? 'active' : '' }}" data-index="{{ $i }}" aria-label="Go to slide {{ $i + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        @else
            <div class="hero-track">
                <div class="hero-slide active">
                    <div class="hero-slide-bg no-image"></div>
                    <div class="hero-overlay"></div>
                    <div class="container hero-container">
                        <div class="hero-body" class="hero-body fallback">
                            <span class="hero-chip">
                                <i class="fas fa-sparkles"></i> Welcome
                            </span>
                            <h1 class="hero-heading">Discover Your Next <span>Great Read</span></h1>
                            <p class="hero-description" >Explore our curated collection of premium books and stationery.</p>
                            <div class="hero-actions" >
                                <a href="{{ route('books.index') }}" class="btn-hero btn-hero-primary">
                                    Explore Books <i class="fas fa-arrow-right"></i>
                                </a>
                                <a href="{{ route('books.index', ['sort' => 'latest']) }}" class="btn-hero btn-hero-ghost">
                                    New Arrivals
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>

    {{-- STATS STRIP --}}
    <section class="stats-strip">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon stat-icon-books">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">{{ $totalBooks }}+</span>
                        <span class="stat-label">Books Available</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon stat-icon-readers">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">{{ $totalCustomers }}+</span>
                        <span class="stat-label">Happy Readers</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon stat-icon-orders">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">{{ $totalOrders }}+</span>
                        <span class="stat-label">Orders Delivered</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon stat-icon-satisfaction">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">4.9/5</span>
                        <span class="stat-label">Customer Rating</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CATEGORIES --}}
    <section class="section categories-section">
        <div class="container">
            <div class="section-heading">
                <span class="section-eyebrow">Browse</span>
                <h2 class="section-title">Shop by Category</h2>
                <p class="section-subtitle">Find exactly what you need from our curated collections</p>
            </div>
            @if($categories->isNotEmpty())
                <div class="categories-image-grid">
                    @foreach($categories as $cat)
                        <a href="{{ route('books.index', ['category' => $cat->id]) }}" class="category-image-card">
                            <div class="category-image-wrapper">
                                <img src="{{ $cat->avatarUrl }}" alt="{{ $cat->name }}" class="category-image-img" loading="lazy">
                                <div class="category-image-overlay"></div>
                            </div>
                            <div class="category-image-body">
                                <h3 class="category-image-name">{{ $cat->name }}</h3>
                                <span class="category-image-count">{{ $cat->books_count }} {{ Str::plural('Book', $cat->books_count) }}</span>
                            </div>
                            <span class="category-image-arrow">
                                Explore <i class="fas fa-arrow-right"></i>
                            </span>
                        </a>
                    @endforeach
                </div>
                <div class="section-view-all-wrapper">
                    <a href="{{ route('books.index') }}" class="section-view-all">
                        View All Categories <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- BEST SELLERS CAROUSEL --}}
    @if($bestSellers->count() > 0)
        <section class="section">
            <div class="container">
                <div class="section-heading section-heading-row">
                    <div class="heading-text-group">
                        <span class="section-eyebrow">Popular</span>
                        <h2 class="section-title">Best Sellers</h2>
                        <p class="section-subtitle">What everyone is reading this month</p>
                    </div>
                </div>
                <div class="carousel-wrapper">
                    <button class="carousel-arrow carousel-arrow-left" id="bestSellersPrev" aria-label="Previous books">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="carousel-track-wrapper">
                        <div class="carousel-track" id="bestSellersTrack">
                            @foreach($bestSellers as $book)
                                @include('components.customer.book-card', ['book' => $book])
                            @endforeach
                        </div>
                    </div>
                    <button class="carousel-arrow carousel-arrow-right" id="bestSellersNext" aria-label="Next books">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                 <div class="section-view-all-wrapper">
                    <a href="{{ route('books.index') }}" class="section-view-all">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- SHOP BY AUTHOR --}}
    @if($topAuthors->isNotEmpty())
        <section class="section section-surface">
            <div class="container">
                <div class="section-heading">
                    <span class="section-eyebrow">Writers</span>
                    <h2 class="section-title">Shop by Author</h2>
                    <p class="section-subtitle">Discover books from your favorite writers</p>
                </div>
                <div class="authors-grid">
                    @foreach($topAuthors as $author)
                        <a href="{{ route('authors.show', $author) }}" class="author-card">
                            <div class="author-card-avatar">
                                <img src="{{ $author->avatarUrl }}" alt="{{ $author->name }}" class="author-card-img">
                                
                            </div>
                            <div class="author-card-info">
                                <h4 class="author-card-name">{{ $author->name }} @if($author->country && $author->country->code)<img src="https://flagcdn.com/24x18/{{ strtolower($author->country->code) }}.png" alt="{{ $author->country->name }}" class="author-card-name-flag" loading="lazy">@endif</h4>
                                <!-- -->{{ $author->books_count }} {{ Str::plural('Book', $author->books_count) }}</span>
                            </div>
                            <span class="author-card-arrow">
                                View Books <i class="fas fa-arrow-right"></i>
                            </span>
                        </a>
                    @endforeach
                </div>
                <div class="section-view-all-wrapper">
                    <a href="{{ route('authors.index') }}" class="section-view-all">
                        View All Authors <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- NEW ARRIVALS CAROUSEL --}}
    @if($newBooks->count() > 0)
        <section class="section section-surface">
            <div class="container">
                <div class="section-heading-row">
                    <div class="heading-text-group">
                        <span class="section-eyebrow">Fresh</span>
                        <h2 class="section-title">New Arrivals</h2>
                        <p class="section-subtitle">Just added to our shelves</p>
                    </div>
                </div>
                <div class="carousel-wrapper">
                    <button class="carousel-arrow carousel-arrow-left" id="newArrivalsPrev" aria-label="Previous books">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="carousel-track-wrapper">
                        <div class="carousel-track" id="newArrivalsTrack">
                            @foreach($newBooks as $book)
                                @include('components.customer.book-card', ['book' => $book])
                            @endforeach
                        </div>
                    </div>
                    <button class="carousel-arrow carousel-arrow-right" id="newArrivalsNext" aria-label="Next books">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="section-view-all-wrapper">
                    <a href="{{ route('books.index', ['sort' => 'latest']) }}" class="section-view-all">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- PROMOTIONS --}}
    <section class="section promotions-section">
        <div class="container">
            <div class="section-heading">
                <span class="section-eyebrow">Offers</span>
                <h2 class="section-title">Special Promotions</h2>
                <p class="section-subtitle">Limited-time deals you will love</p>
            </div>
            <div class="promotions-grid">
                <div class="promo-card promo-card-primary">
                    <div class="promo-content">
                        <span class="promo-badge"><i class="fas fa-bolt"></i> Limited Time</span>
                        <h3>Free Shipping</h3>
                        <p>On orders over 50,000 MMK</p>
                        <a href="{{ route('books.index') }}" class="promo-link">
                            Shop Now <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="promo-visual">
                        <i class="fas fa-truck-fast"></i>
                    </div>
                </div>
                <div class="promo-card promo-card-secondary">
                    <div class="promo-content">
                        <span class="promo-badge"><i class="fas fa-gift"></i> New Customer</span>
                        <h3>10% Off First Order</h3>
                        <p>Use code: <strong>WELCOME10</strong></p>
                        <a href="{{ route('books.index') }}" class="promo-link">
                            Claim Offer <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="promo-visual">
                        <i class="fas fa-tag"></i>
                    </div>
                </div>
                <div class="promo-card promo-card-accent">
                    <div class="promo-content">
                        <span class="promo-badge"><i class="fas fa-star"></i> Bestseller</span>
                        <h3>Bundle & Save</h3>
                        <p>Buy 3 books, get 15% off</p>
                        <a href="{{ route('books.index') }}" class="promo-link">
                            Browse Bundles <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="promo-visual">
                        <i class="fas fa-layer-group"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- TRUST --}}
    <section class="section trust-section">
        <div class="container">
            <div class="section-heading">
                <span class="section-eyebrow">Why Bookshop</span>
                <h2 class="section-title">The Bookshop Difference</h2>
                <p class="section-subtitle">We are committed to delivering excellence at every step</p>
            </div>
            <div class="trust-grid">
                <div class="trust-card">
                    <div class="trust-icon trust-icon-quality">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h4>Premium Quality</h4>
                    <p>Curated collection from top publishers worldwide, every book hand-picked for excellence.</p>
                </div>
                <div class="trust-card">
                    <div class="trust-icon trust-icon-payment">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <h4>Secure Payments</h4>
                    <p>Stripe, KPay, Wave Pay & COD — bank-level encryption on every transaction.</p>
                </div>
                <div class="trust-card">
                    <div class="trust-icon trust-icon-delivery">
                        <i class="fas fa-box"></i>
                    </div>
                    <h4>Lightning Delivery</h4>
                    <p>Free shipping on orders over 50,000 MMK. Most orders arrive within 2-3 business days.</p>
                </div>
                <div class="trust-card">
                    <div class="trust-icon trust-icon-support">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4>24/7 Support</h4>
                    <p>Our dedicated team is always ready to assist you — anytime, any day.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- NEWSLETTER --}}
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-card">
                <div class="newsletter-content">
                    <div class="newsletter-icon">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>
                    <h2>Stay in the Loop</h2>
                    <p>Subscribe for exclusive deals, new arrivals, and literary inspiration delivered to your inbox.</p>
                    <form class="newsletter-form" onsubmit="handleNewsletter(event)">
                        @csrf
                        <div class="newsletter-input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" placeholder="Enter your email address" required>
                            <button type="submit" class="btn-newsletter">
                                Subscribe <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script src="{{ asset('js/customer/home.js') }}"></script>
    @if(session('login_error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (typeof openLoginModal === 'function') {
                    openLoginModal();
                }
                const errorEl = document.getElementById('loginError');
                if (errorEl) {
                    errorEl.style.display = 'block';
                    errorEl.innerHTML = '<i class="fas fa-circle-exclamation"></i> {{ session('login_error') }}';
                }
            }, 100);
        });
    </script>
@endif
@endpush{{-- DUMMY LINE FOR SED REPLACEMENT --}}
