<div class="book-card" data-book-id="{{ $book->id }}">
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
            <button class="book-card-cart btn-add-cart" data-book-id="{{ $book->id }}" aria-label="Add to cart">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
</div>
