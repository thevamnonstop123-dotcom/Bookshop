@extends('layouts.customer')

@section('title', 'My Wishlist — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/wishlist.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')

<div class="wishlist-page">
    <div class="container">
        {{-- Header --}}
        <div class="wishlist-header">
            <div class="wishlist-title-group">
                <h2><i class="fas fa-heart wishlist-title-icon"></i> My Wishlist</h2>
                <p>Books you have saved for later</p>
            </div>
            <span class="wishlist-count">{{ $wishlists->count() }} {{ Str::plural('book', $wishlists->count()) }}</span>
        </div>

        @if($wishlists->count() > 0)
            {{-- Grid --}}
            <div class="wishlist-grid">
                @foreach($wishlists as $wish)
                    @php $book = $wish->book; $avail = $book->availability; @endphp
                    <div class="wishlist-item" id="wishlist-item-{{ $book->id }}">
                        {{-- Image --}}
                        <div class="wishlist-item-image-wrapper">
                            <a href="{{ route('books.show', $book->slug) }}" class="wishlist-item-image">
                                <img src="{{ $book->image && $book->image !== 'default.png' ? asset('storage/'.$book->image) : 'https://placehold.co/400x560/F1F5F9/1E3A8A?text='.urlencode($book->title) }}" 
                                     alt="{{ $book->title }}" 
                                     loading="lazy">
                                @if($book->isOnSale())
                                    <span class="wishlist-item-badge">-{{ $book->discountPercentage() }}%</span>
                                @endif
                            </a>
                        </div>

                        {{-- Info --}}
                        <div class="wishlist-item-info">
                            <span class="wishlist-item-category">{{ $book->category?->name ?? 'General' }}</span>
                            
                            <a href="{{ route('books.show', $book->slug) }}" class="wishlist-item-title">{{ $book->title }}</a>
                            
                            <span class="wishlist-item-author">
                                <i class="fas fa-user"></i> {{ $book->authors->first()->name ?? 'Unknown' }}
                            </span>
                            
                            {{-- Rating --}}
                            @if($book->rating_count > 0)
                                <div class="wishlist-item-rating">
                                    <span class="wishlist-item-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($book->rating))
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </span>
                                    <span class="wishlist-item-rating-value">{{ number_format($book->rating, 1) }}</span>
                                    <span class="wishlist-item-rating-count">({{ $book->rating_count }})</span>
                                </div>
                            @endif

                            {{-- Stock --}}
                            <span class="wishlist-stock {{ $avail['status'] }}">
                                <i class="fas {{ $avail['icon'] }}"></i> {{ $avail['label'] }}
                            </span>

                            {{-- Price --}}
                            <div class="wishlist-item-price">
                                @if($book->isOnSale())
                                    <span class="price-original">{{ number_format($book->price) }} MMK</span>
                                    <span class="price-sale">{{ number_format($book->sale_price) }} MMK</span>
                                @else
                                    <span class="price-regular">{{ number_format($book->price) }} MMK</span>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="wishlist-item-actions">
                                @if($book->isPurchasable())
                                    <div class="wishlist-qty-wrapper">
                                        <button class="wishlist-qty-btn wishlist-qty-minus" data-book-id="{{ $book->id }}" onclick="changeWishlistQty(this, -1, {{ $book->id }})">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="wishlist-qty-input" id="wishlist-qty-{{ $book->id }}" value="1" min="1" max="{{ $book->stock_quantity }}" readonly>
                                        <button class="wishlist-qty-btn wishlist-qty-plus" data-book-id="{{ $book->id }}" onclick="changeWishlistQty(this, 1, {{ $book->id }})">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <button class="wishlist-btn-cart" data-book-id="{{ $book->id }}" onclick="addWishlistToCart(this, {{ $book->id }})">
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </button>
                                @endif
                                <button class="wishlist-btn-remove" data-book-id="{{ $book->id }}" onclick="removeWishlistItem(this, {{ $book->id }})" title="Remove from wishlist">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-heart"></i></div>
                <h3>Your wishlist is empty</h3>
                <p>Start exploring and save books you love!</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary">
                    <i class="fas fa-book-open"></i> Browse Books
                </a>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/customer/wishlist.js') }}"></script>
@endpush