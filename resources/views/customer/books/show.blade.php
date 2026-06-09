@extends('layouts.customer')

@section('title', $book->title . ' - Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/book-detail.css') }}">
@endpush

@section('content')

<div class="book-detail-page">
    <div class="container">

        {{-- Breadcrumb --}}
        <div class="book-breadcrumb">
            <a href="{{ route('customer.home') }}">Home</a>
            <span>/</span>
            <a href="{{ route('books.index') }}">Books</a>
            <span>/</span>
            <span>{{ $book->title }}</span>
        </div>

        <div class="book-detail-layout">

            {{-- Cover --}}
            <div class="book-detail-cover">
                <img src="{{ $book->image && $book->image !== 'default.png' ? asset('storage/'.$book->image) : 'https://placehold.co/600x800/1e293b/f59e0b?text='.urlencode($book->title) }}"
                     alt="{{ $book->title }}">
            </div>

            {{-- Info --}}
            <div class="book-detail-info">

                <span class="book-detail-category">{{ $book->category?->name }}</span>
                <h1>{{ $book->title }}</h1>

                <div class="book-detail-authors">
                    By <span>{{ $book->authors->pluck('name')->join(', ') }}</span>
                </div>

                {{-- Meta Grid --}}
                <div class="book-meta-grid">
                    <div class="book-meta-item">
                        <div class="book-meta-label">ISBN</div>
                        <div class="book-meta-value">{{ $book->isbn }}</div>
                    </div>
                    <div class="book-meta-item">
                        <div class="book-meta-label">Language</div>
                        <div class="book-meta-value">{{ $book->language }}</div>
                    </div>
                    <div class="book-meta-item">
                        <div class="book-meta-label">Published</div>
                        <div class="book-meta-value">{{ $book->published_date->format('d M Y') }}</div>
                    </div>
                    <div class="book-meta-item">
                        <div class="book-meta-label">Category</div>
                        <div class="book-meta-value">{{ $book->category?->name }}</div>
                    </div>
                </div>

                {{-- Price Section --}}
                <div class="book-detail-price-section">
                    <div class="book-detail-price-row">
                        @if($book->isOnSale())
                            <span class="book-detail-original">{{ number_format($book->price) }} MMK</span>
                            <span class="book-detail-price" style="color:#ef4444;">{{ number_format($book->sale_price) }} MMK</span>
                            <span class="book-detail-save">
                                <i class="fas fa-tag"></i> Save {{ $book->discountPercentage() }}%
                            </span>
                        @else
                            <span class="book-detail-price">{{ number_format($book->price) }} MMK</span>
                        @endif
                    </div>

                    @if($book->isInStock())
                        @if($book->stock_quantity <= 5)
                            <span class="stock-badge low-stock">
                                <i class="fas fa-clock"></i> Only {{ $book->stock_quantity }} left in stock
                            </span>
                        @else
                            <span class="stock-badge in-stock">
                                <i class="fas fa-check-circle"></i> In Stock ({{ $book->stock_quantity }} available)
                            </span>
                        @endif
                    @else
                        <span class="stock-badge out-of-stock">
                            <i class="fas fa-times-circle"></i> Out of Stock
                        </span>
                    @endif
                </div>

                {{-- Actions --}}
                @if($book->isInStock())
                    <div class="book-detail-actions">
                        <div class="quantity-selector">
                            <button type="button" onclick="changeQty(-1)">−</button>
                            <input type="number" id="quantity" value="1" min="1" max="{{ $book->stock_quantity }}" readonly>
                            <button type="button" onclick="changeQty(1)">+</button>
                        </div>
                        <button class="btn btn-accent btn-add-cart" data-book-id="{{ $book->id }}">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button class="btn btn-primary btn-buy-now" onclick="document.querySelector('.btn-add-cart').click(); setTimeout(() => window.location='{{ route('checkout.index') }}', 500);">
                            <i class="fas fa-bolt"></i> Buy Now
                        </button>
                    </div>
                @else
                    <div class="book-detail-actions">
                        <button class="btn btn-outline" disabled style="flex:1;padding:14px 28px;font-size:15px;">
                            <i class="fas fa-ban"></i> Out of Stock — Notify Me
                        </button>
                    </div>
                @endif

                {{-- Description --}}
                @if($book->description)
                    <div class="book-detail-description">
                        <h3>About This Book</h3>
                        <p>{{ $book->description }}</p>
                    </div>
                @endif

            </div>
        </div>

        {{-- Related Books --}}
        @if($relatedBooks->count() > 0)
            <div class="related-section">
                <div class="section-header">
                    <h2>You May Also Like</h2>
                </div>
                <div class="related-grid">
                    @foreach($relatedBooks as $related)
                        <div class="book-card">
                            <a href="{{ route('books.show', $related->slug) }}">
                                <img src="{{ $related->image && $related->image !== 'default.png' ? asset('storage/'.$related->image) : 'https://placehold.co/400x560/1e293b/f59e0b?text='.urlencode($related->title) }}"
                                     alt="{{ $related->title }}" class="book-cover">
                            </a>
                            <div class="book-info">
                                <div class="book-title">{{ $related->title }}</div>
                                <div class="book-author">{{ $related->authors->first()->name ?? 'Unknown' }}</div>
                                <div class="book-price">{{ number_format($related->price) }} MMK</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/customer/cart.js') }}"></script>
<script>
    function changeQty(amount) {
        const input = document.getElementById('quantity');
        let value = parseInt(input.value) + amount;
        const max = {{ $book->stock_quantity }};
        if (value < 1) value = 1;
        if (value > max) value = max;
        input.value = value;
    }
</script>
@endpush