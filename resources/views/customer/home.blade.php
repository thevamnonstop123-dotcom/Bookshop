@extends('layouts.customer')

@section('title', 'Bookshop - Your Premium Online Bookstore')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/home.css') }}">
@endpush

@section('content')

    {{-- HERO BANNER --}}
    @php $banners = \App\Models\Banner::where('status','active')->whereDate('start_date','<=',now())->whereDate('end_date','>=',now())->orderBy('display_order')->get(); @endphp
    @if($banners->count() > 0)
        <div class="hero-slider">
            @foreach($banners as $banner)
                @php $hasImage = $banner->image && Storage::disk('public')->exists($banner->image); @endphp
                <div class="hero-slide {{ $loop->first ? 'active' : '' }} {{ !$hasImage ? 'no-image' : '' }}"
                     style="{{ $hasImage ? "background-image:url('".asset('storage/'.$banner->image)."');" : '' }}">
                    <div class="container">
                        <div class="hero-content">
                            <div class="hero-text">
                                <span class="hero-badge">📚 {{ $banner->title }}</span>
                                <h2>{{ $banner->title }}</h2>
                                <p>{{ $banner->description }}</p>
                                <div class="hero-buttons">
                                    <a href="{{ route('books.index') }}" class="btn btn-accent">Shop Now</a>
                                    <a href="{{ route('books.index', ['sort'=>'latest']) }}" class="btn btn-outline-light">New Arrivals</a>
                                </div>
                            </div>
                            @if($hasImage)
                                <div class="hero-image">
                                    <img src="{{ asset('storage/'.$banner->image) }}" alt="{{ $banner->title }}">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
            @if($banners->count() > 1)
                <div class="hero-dots">
                    @foreach($banners as $i => $b)
                        <span class="hero-dot {{ $loop->first ? 'active' : '' }}" data-slide="{{ $i }}"></span>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <div class="hero-slider">
            <div class="hero-slide active no-image">
                <div class="container">
                    <div class="hero-content">
                        <div class="hero-text">
                            <span class="hero-badge">📚 Welcome</span>
                            <h2>Discover Your Next <span>Great Read</span></h2>
                            <p>Explore our curated collection of premium books.</p>
                            <div class="hero-buttons">
                                <a href="{{ route('books.index') }}" class="btn btn-accent">Explore Books</a>
                                <a href="{{ route('books.index', ['sort'=>'latest']) }}" class="btn btn-outline-light">New Arrivals</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- SOCIAL PROOF --}}
    @php $totalBooks = \App\Models\Book::where('status','active')->count(); @endphp
    @php $totalCustomers = \App\Models\Customer::count(); @endphp
    @php $totalOrders = \App\Models\Order::count(); @endphp
    <div class="social-strip">
        <div class="container">
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-number">{{ $totalBooks }}+</div>
                    <div class="stat-label">Books Available</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $totalCustomers }}+</div>
                    <div class="stat-label">Happy Readers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $totalOrders }}+</div>
                    <div class="stat-label">Orders Delivered</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">99%</div>
                    <div class="stat-label">Satisfaction Rate</div>
                </div>
            </div>
        </div>
    </div>

    {{-- CATEGORIES --}}
    @php $categories = \App\Models\Category::where('status','active')->get(); @endphp
    @if($categories->count() > 0)
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <span class="section-tag">Explore</span>
                    <h2>Browse Categories</h2>
                    <p>Find books by your favorite topic</p>
                </div>
                <div class="category-grid">
                    @foreach($categories as $cat)
                        <a href="{{ route('books.index', ['category'=>$cat->id]) }}" class="category-card">
                            <div class="cat-icon"><i class="fas fa-layer-group"></i></div>
                            <h4>{{ $cat->name }}</h4>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- NEW ARRIVALS --}}
    @php $newBooks = \App\Models\Book::with('authors')->where('status','active')->latest()->limit(5)->get(); @endphp
    @if($newBooks->count() > 0)
        <section class="section section-alt">
            <div class="container">
                <div class="section-header">
                    <span class="section-tag">Fresh</span>
                    <h2>New Arrivals</h2>
                    <p>Just added to our collection</p>
                </div>
                <div class="book-grid">
                    @foreach($newBooks as $book)
                        <div class="book-card">
                            <a href="{{ route('books.show', $book->slug) }}">
                                <div class="book-cover-wrap">
                                    @if($book->isOnSale())
                                        <span class="sale-badge">-{{ $book->discountPercentage() }}%</span>
                                    @endif
                                    <img src="{{ $book->image && $book->image !== 'default.png' ? asset('storage/'.$book->image) : 'https://placehold.co/400x560/1e293b/f59e0b?text='.urlencode($book->title) }}"
                                         alt="{{ $book->title }}" class="book-cover">
                                </div>
                            </a>
                            <div class="book-info">
                                <div class="book-category">{{ $book->category?->name }}</div>
                                <div class="book-title">{{ $book->title }}</div>
                                <div class="book-author">{{ $book->authors->first()->name ?? 'Unknown' }}</div>
                                <div class="book-footer">
                                    <span class="book-price">
                                        @if($book->isOnSale())
                                            <span class="original">{{ number_format($book->price) }}</span>
                                            <span class="sale">{{ number_format($book->sale_price) }} MMK</span>
                                        @else
                                            {{ number_format($book->price) }} MMK
                                        @endif
                                    </span>
                                    <button class="btn btn-accent btn-sm btn-add-cart" data-book-id="{{ $book->id }}">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- BEST SELLERS --}}
    @php $bestSellers = \App\Models\Book::with('authors')->where('status','active')->inRandomOrder()->limit(5)->get(); @endphp
    @if($bestSellers->count() > 0)
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <span class="section-tag">Popular</span>
                    <h2>Best Sellers</h2>
                    <p>Most popular this month</p>
                </div>
                <div class="book-grid">
                    @foreach($bestSellers as $book)
                        <div class="book-card">
                            <a href="{{ route('books.show', $book->slug) }}">
                                <div class="book-cover-wrap">
                                    @if($book->isOnSale())
                                        <span class="sale-badge">-{{ $book->discountPercentage() }}%</span>
                                    @endif
                                    <img src="{{ $book->image && $book->image !== 'default.png' ? asset('storage/'.$book->image) : 'https://placehold.co/400x560/1e293b/f59e0b?text='.urlencode($book->title) }}"
                                         alt="{{ $book->title }}" class="book-cover">
                                </div>
                            </a>
                            <div class="book-info">
                                <div class="book-category">{{ $book->category?->name }}</div>
                                <div class="book-title">{{ $book->title }}</div>
                                <div class="book-author">{{ $book->authors->first()->name ?? 'Unknown' }}</div>
                                <div class="book-footer">
                                    <span class="book-price">
                                        @if($book->isOnSale())
                                            <span class="original">{{ number_format($book->price) }}</span>
                                            <span class="sale">{{ number_format($book->sale_price) }} MMK</span>
                                        @else
                                            {{ number_format($book->price) }} MMK
                                        @endif
                                    </span>
                                    <button class="btn btn-accent btn-sm btn-add-cart" data-book-id="{{ $book->id }}">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- TRUST BADGES --}}
    <section class="trust-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Why Us</span>
                <h2>Why Choose Bookshop</h2>
            </div>
            <div class="trust-grid">
                <div class="trust-card">
                    <div class="trust-icon quality"><i class="fas fa-medal"></i></div>
                    <h4>Quality Books</h4>
                    <p>Carefully curated collection from top publishers worldwide</p>
                </div>
                <div class="trust-card">
                    <div class="trust-icon payment"><i class="fas fa-shield-halved"></i></div>
                    <h4>Secure Payment</h4>
                    <p>Stripe, KPay, Wave Pay & COD — your data is always safe</p>
                </div>
                <div class="trust-card">
                    <div class="trust-icon delivery"><i class="fas fa-truck-fast"></i></div>
                    <h4>Fast Delivery</h4>
                    <p>Free shipping on orders over 50,000 MMK</p>
                </div>
                <div class="trust-card">
                    <div class="trust-icon support"><i class="fas fa-headset"></i></div>
                    <h4>24/7 Support</h4>
                    <p>Our team is always ready to help you</p>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script src="{{ asset('js/customer/cart.js') }}"></script>
<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-dot');
    function showSlide(i) {
        slides.forEach((s,idx) => s.classList.toggle('active', idx===i));
        dots.forEach((d,idx) => d.classList.toggle('active', idx===i));
    }
    if(dots.length > 1) {
        dots.forEach(dot => dot.addEventListener('click', function() {
            currentSlide = parseInt(this.dataset.slide);
            showSlide(currentSlide);
        }));
        setInterval(() => { currentSlide = (currentSlide+1) % slides.length; showSlide(currentSlide); }, 4000);
    }
</script>
@endpush