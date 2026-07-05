@extends('layouts.customer')

@section('title', 'My Wishlist — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/wishlist.css') }}">
@endpush

@section('content')

<div class="wishlist-page">
    <div class="container">
        <div class="wishlist-header">
            <div class="wishlist-title-group">
                <h2>My Wishlist</h2>
                <p>Books you have saved for later</p>
            </div>
            <span class="wishlist-count">{{ $wishlists->count() }} {{ Str::plural('book', $wishlists->count()) }}</span>
        </div>

        @if($wishlists->count() > 0)
            <div class="wishlist-grid">
                @foreach($wishlists as $wish)
                    @php $book = $wish->book; $avail = $book->availability; @endphp
                    <div class="wishlist-item" id="wishlist-item-{{ $book->id }}">
                        <a href="{{ route('books.show', $book->slug) }}" class="wishlist-item-image">
                            <img src="{{ $book->image && $book->image !== 'default.png' ? asset('storage/'.$book->image) : 'https://placehold.co/200x280/F1F5F9/1E3A8A?text='.urlencode($book->title) }}" alt="{{ $book->title }}" loading="lazy">
                        </a>
                        <div class="wishlist-item-info">
                            <span class="wishlist-item-category">{{ $book->category?->name ?? 'General' }}</span>
                            <a href="{{ route('books.show', $book->slug) }}" class="wishlist-item-title">{{ $book->title }}</a>
                            <span class="wishlist-item-author">by {{ $book->authors->first()->name ?? 'Unknown' }}</span>
                            
                            @if($book->rating_count > 0)
                                <div class="wishlist-item-rating">
                                    <span class="wishlist-item-stars">★ {{ number_format($book->rating, 1) }}</span>
                                    <span>({{ $book->rating_count }})</span>
                                </div>
                            @endif

                            <span class="wishlist-stock {{ $avail['status'] === 'in_stock' ? 'in_stock' : ($avail['status'] === 'low_stock' ? 'low_stock' : 'out') }}">
                                {{ $avail['label'] }}
                            </span>

                            <div class="wishlist-item-price">
                                @if($book->isOnSale())
                                    <span class="original">{{ number_format($book->price) }} MMK</span>
                                    <span class="sale">{{ number_format($book->sale_price) }} MMK</span>
                                @else
                                    {{ number_format($book->price) }} MMK
                                @endif
                            </div>

                            <div class="wishlist-item-actions">
                                @if($book->isPurchasable())
                                    <button class="wishlist-btn-cart btn-add-cart" data-book-id="{{ $book->id }}">
                                        Add to Cart
                                    </button>
                                @endif
                                <button class="wishlist-btn-remove" data-book-id="{{ $book->id }}" onclick="removeWishlistItem(this, {{ $book->id }})" title="Remove from wishlist">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-heart"></i></div>
                <h3>Your wishlist is empty</h3>
                <p>Start exploring and save books you love!</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary" style="margin-top:16px;">Browse Books</a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/customer/wishlist.js') }}"></script>
@endpush
