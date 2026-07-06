@php
    $availability = $book->availability;
    $isOutOfStock = $availability['status'] === 'out_of_stock';
    $isDiscontinued = $availability['status'] === 'discontinued';
    $showGrayEffect = $isOutOfStock || $isDiscontinued;
@endphp

<div class="book-card {{ $showGrayEffect ? 'book-card-unavailable' : '' }}" data-book-id="{{ $book->id }}">
    {{-- Cover Image --}}
    <a href="{{ route('books.show', $book->slug) }}" class="book-card-cover" aria-label="{{ $book->title }}">
        <img src="{{ $book->image && $book->image !== 'default.png' ? asset('storage/'.$book->image) : 'https://placehold.co/400x560/F1F5F9/1E3A8A?text='.urlencode($book->title) }}"
             alt="{{ $book->title }}"
             class="book-card-img"
             loading="lazy">
        
        {{-- Sale Badge --}}
        @if($book->isOnSale())
            <span class="book-card-badge">-{{ $book->discountPercentage() }}%</span>
        @endif

        {{-- Availability Badge --}}
        @if(in_array($availability['status'], ['out_of_stock', 'coming_soon', 'pre_order', 'discontinued']))
            <span class="book-card-badge book-card-badge-{{ $availability['status'] }}" 
                  style="background:{{ $availability['color'] }};color:#fff;">
                {{ $availability['label'] }}
            </span>
        @endif
        
        @if($availability['status'] === 'low_stock')
            <span class="book-card-badge book-card-badge-low-stock">
                {{ $availability['label'] }}
            </span>
        @endif
    </a>

    {{-- Wishlist --}}
    <button class="book-card-wishlist {{ in_array($book->id, $wishlistedIds ?? []) ? 'active' : '' }}"
            data-book-id="{{ $book->id }}" 
            onclick="toggleWishlist(this, {{ $book->id }})" 
            aria-label="Wishlist">
        <i class="{{ in_array($book->id, $wishlistedIds ?? []) ? 'fas' : 'far' }} fa-heart"></i>
    </button>

    {{-- Body --}}
    <div class="book-card-body">
        <span class="book-card-category">{{ $book->category?->name ?? 'General' }}</span>
        <h3 class="book-card-title">
            <a href="{{ route('books.show', $book->slug) }}">{{ $book->title }}</a>
        </h3>
        <p class="book-card-author">{{ $book->authors->first()->name ?? 'Unknown Author' }}</p>

        <div class="book-card-footer">
            <div class="book-card-price">
                @if($book->isOnSale())
                    <span class="price-original">{{ number_format($book->price) }} MMK</span>
                    <span class="price-sale">{{ number_format($book->sale_price) }} MMK</span>
                @else
                    <span class="price-regular">{{ number_format($book->price) }} MMK</span>
                @endif
            </div>

            {{-- Cart Button --}}
            @if($book->isPurchasable())
                <button class="book-card-cart btn-add-cart" data-book-id="{{ $book->id }}" aria-label="Add to cart">
                    <i class="fas fa-shopping-cart"></i>
                </button>
            @elseif($availability['status'] === 'pre_order')
                <button class="book-card-cart book-card-cart-preorder" data-book-id="{{ $book->id }}" aria-label="Pre-order">
                    <i class="fas fa-cart-arrow-down"></i>
                </button>
            @else
                <button class="book-card-cart book-card-cart-disabled" disabled aria-label="{{ $availability['label'] }}">
                    <i class="fas fa-{{ $availability['icon'] }}"></i>
                </button>
            @endif
        </div>
    </div>
</div>
