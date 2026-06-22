<div class="book-card" data-book-id="{{ $book->id }}">
    <a href="{{ route('books.show', $book->slug) }}" class="book-card-cover">
        <div class="book-cover-frame">
            @if($book->isOnSale())
                <span class="book-badge">-{{ $book->discountPercentage() }}%</span>
            @endif
            <img src="{{ $book->image && $book->image !== 'default.png' ? asset('storage/'.$book->image) : 'https://placehold.co/400x560/0F172A/FFFFFF?text='.urlencode($book->title) }}"
                 alt="{{ $book->title }}"
                 class="book-cover-img"
                 loading="lazy">
        </div>
    </a>
    <div class="book-card-body">
        <span class="book-category-tag">{{ $book->category?->name ?? 'General' }}</span>
        <h3 class="book-card-title">
            <a href="{{ route('books.show', $book->slug) }}">{{ $book->title }}</a>
        </h3>
        <p class="book-card-author">by {{ $book->authors->first()->name ?? 'Unknown Author' }}</p>
        <div class="book-card-footer">
    <div class="book-card-price">
        @if($book->isOnSale())
            <span class="price-original">{{ number_format($book->price) }} MMK</span>
            <span class="price-sale">{{ number_format($book->sale_price) }} MMK</span>
        @else
            <span class="price-regular">{{ number_format($book->price) }} MMK</span>
        @endif
    </div>
    <div style="display:flex;gap:6px;align-items:center;">
        @auth('customer')
            <button class="btn-wishlist-toggle {{ in_array($book->id, $wishlistedIds ?? []) ? 'wishlisted' : '' }}"
                    data-book-id="{{ $book->id }}" onclick="toggleWishlist(this, {{ $book->id }})" aria-label="Wishlist">
                <i class="far fa-heart"></i>
            </button>
        @endauth
        <button class="btn-cart-icon btn-add-cart" data-book-id="{{ $book->id }}" aria-label="Add to cart">
            <i class="fas fa-bag-shopping"></i>
        </button>
    </div>
</div>
    </div>
</div>