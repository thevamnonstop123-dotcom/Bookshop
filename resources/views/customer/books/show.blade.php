@extends('layouts.customer')

@section('title', $book->title . ' — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/book-detail.css') }}">
@endpush

@section('content')

<div class="book-detail-page">
    <div class="container">

        {{-- Breadcrumb --}}
        <nav class="detail-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('customer.home') }}"><i class="fas fa-home"></i> Home</a>
            <i class="fas fa-chevron-right"></i>
            <a href="{{ route('books.index') }}">Books</a>
            @if($book->category)
                <i class="fas fa-chevron-right"></i>
                <a href="{{ route('books.index', ['category' => $book->category->id]) }}">{{ $book->category->name }}</a>
            @endif
            <i class="fas fa-chevron-right"></i>
            <span>{{ $book->title }}</span>
        </nav>

        {{-- Main Detail --}}
        <div class="detail-layout">

            {{-- Cover --}}
            <div class="detail-cover">
                <div class="detail-cover-frame">
                    @if($book->isOnSale())
                        <span class="detail-cover-badge">-{{ $book->discountPercentage() }}%</span>
                    @endif
                    <img src="{{ $book->image && $book->image !== 'default.png' ? asset('storage/'.$book->image) : 'https://placehold.co/600x800/0F172A/FFFFFF?text='.urlencode($book->title) }}"
                         alt="{{ $book->title }}"
                         class="detail-cover-img">
                </div>
                @if($book->isEbook())
                    <div class="detail-cover-format">
                        <i class="fas fa-tablet-screen-button"></i> E-Book Edition
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="detail-info">

                {{-- Category & Format --}}
                <div class="detail-meta-top">
                    <span class="detail-category-tag">{{ $book->category?->name ?? 'Uncategorized' }}</span>
                    <div class="detail-format-inline">
                        @php
                            $hasPhysical = $book->isInStock();
                            $hasEbook = $book->isEbook();
                        @endphp

                        {{-- If both available, show a switcher for UI-only format selection --}}
                        @if($hasPhysical && $hasEbook)
                            <div class="detail-format-switch" data-book-id="{{ $book->id }}">
                                <button class="format-option active" data-format="physical"><i class="fas fa-book"></i> Physical</button>
                                <button class="format-option" data-format="ebook"><i class="fas fa-tablet-screen-button"></i> E‑Book</button>
                            </div>
                        @elseif($hasEbook)
                            <span class="detail-format-tag detail-format-ebook">
                                <i class="fas fa-bolt"></i> E-Book
                            </span>
                        @else
                            <span class="detail-format-tag detail-format-physical">
                                <i class="fas fa-book"></i> Physical
                            </span>
                        @endif

                    </div>
                </div>

                {{-- Title --}}
                <h1 class="detail-title">{{ $book->title }}</h1>

                {{-- Authors --}}
                <div class="detail-authors">
                    <span>By</span>
                    <span class="detail-authors-list">{{ $book->authors->pluck('name')->join(', ') }}</span>
                </div>

                {{-- Meta Grid --}}
                <div class="detail-meta-grid">
                    <div class="detail-meta-item">
                        <span class="detail-meta-label">ISBN</span>
                        <span class="detail-meta-value">{{ $book->isbn }}</span>
                    </div>
                    <div class="detail-meta-item">
                        <span class="detail-meta-label">Language</span>
                        <span class="detail-meta-value">{{ $book->language }}</span>
                    </div>
                    <div class="detail-meta-item">
                        <span class="detail-meta-label">Published</span>
                        <span class="detail-meta-value">{{ $book->published_date->format('d M Y') }}</span>
                    </div>
                    <div class="detail-meta-item">
                        <span class="detail-meta-label">Format</span>
                        <span class="detail-meta-value">{{ $book->isEbook() ? 'E-Book (Instant)' : 'Paperback' }}</span>
                    </div>
                </div>

                {{-- Price Section --}}
                <div class="detail-price-section">
                    @if($book->isOnSale())
                        <div class="detail-price-row">
                            <span class="detail-price-original">{{ number_format($book->price) }} MMK</span>
                            <span class="detail-price-sale">{{ number_format($book->sale_price) }} MMK</span>
                            <span class="detail-price-save">
                                <i class="fas fa-tag"></i> Save {{ $book->discountPercentage() }}%
                            </span>
                        </div>
                    @else
                        <div class="detail-price-row">
                            <span class="detail-price-current">{{ number_format($book->price) }} MMK</span>
                        </div>
                    @endif

                    {{-- Stock Status --}}
                    @if($book->isEbook())
                        <div class="detail-stock detail-stock-ebook">
                            <i class="fas fa-infinity"></i> Instant access — read anytime
                        </div>
                    @elseif($book->isInStock())
                        @if($book->stock_quantity <= 5)
                            <div class="detail-stock detail-stock-low">
                                <i class="fas fa-exclamation-triangle"></i> Only {{ $book->stock_quantity }} left in stock
                            </div>
                        @else
                            <div class="detail-stock detail-stock-in">
                                <i class="fas fa-check-circle"></i> In stock ({{ $book->stock_quantity }} available)
                            </div>
                        @endif
                    @else
                        <div class="detail-stock detail-stock-out">
                            <i class="fas fa-times-circle"></i> Out of stock
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                @if($book->isEbook() || $book->isInStock())
                    <div class="detail-actions">
                        @if(!$book->isEbook())
                            <div class="detail-qty" id="quantitySelector">
                                <button type="button" class="detail-qty-btn" onclick="changeQuantity(-1)" aria-label="Decrease quantity">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" id="quantity" class="detail-qty-input" value="1"
                                       min="1" max="{{ $book->stock_quantity }}" readonly>
                                <button type="button" class="detail-qty-btn" onclick="changeQuantity(1)" aria-label="Increase quantity">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        @endif
                        <button class="detail-action-btn detail-action-cart btn-add-cart" data-book-id="{{ $book->id }}">
                            <i class="fas fa-shopping-bag"></i> Add to Cart
                        </button>
                        <button class="detail-action-btn detail-action-buy" id="buyNowBtn">
                            <i class="fas fa-bolt"></i> Buy Now
                        </button>
                    </div>
                @else
                    <div class="detail-actions">
                        <button class="detail-action-btn detail-action-disabled" disabled>
                            <i class="fas fa-ban"></i> Out of Stock
                        </button>
                    </div>
                @endif

                {{-- Description --}}
                @if($book->description)
                    <div class="detail-description">
                        <h3 class="detail-description-heading">About This Book</h3>
                        <div class="detail-description-body">
                            <p>{{ $book->description }}</p>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        {{-- Related Books --}}
        @if($relatedBooks->count() > 0)
            <section class="detail-related">
                <div class="detail-related-header">
                    <h2>You May Also Like</h2>
                    <a href="{{ route('books.index') }}" class="detail-related-all">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="detail-related-grid">
                    @foreach($relatedBooks as $related)
                        @include('components.customer.book-card', ['book' => $related])
                    @endforeach
                </div>
            </section>
        @endif

    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/customer/book-detail.js') }}"></script>
@endpush