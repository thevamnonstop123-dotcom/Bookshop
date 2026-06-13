<div class="book-card" data-book-id="{{ $book->id }}">
    <a href="{{ route('books.show', $book->slug) }}" class="book-card-cover">
        <div class="book-cover-frame">
            @if($book->isOnSale())
                <span class="book-badge">-{{ $book->discountPercentage() }}%</span>
            @endif

            {{-- Format badge: E-Book / Physical / Both --}}
            @php
                $hasPhysical = $book->isInStock();
                $hasEbook = $book->isEbook();
            @endphp
            @if($hasPhysical && $hasEbook)
                <span class="book-format-badge">E‑Book • Physical</span>
            @elseif($hasEbook)
                <span class="book-format-badge ebook">E‑Book</span>
            @else
                <span class="book-format-badge physical">Physical</span>
            @endif

            <img src="{{ $book->image && $book->image !== 'default.png' ? asset('storage/'.$book->image) : 'https://placehold.co/400x560/0F172A/FFFFFF?text='.urlencode($book->title) }}"
                 alt="{{ $book->title }}"
                 class="book-cover-img"
                 loading="lazy">

            <div class="book-cover-overlay" aria-hidden="true">
                <div class="book-cover-actions">
                    <button class="overlay-add-cart" data-book-id="{{ $book->id }}" aria-label="Add to cart">
                        <i class="fas fa-shopping-bag"></i> Add
                    </button>
                    <a href="{{ route('books.show', $book->slug) }}" class="overlay-details">Details</a>
                </div>
            </div>
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
            <button class="btn-cart-icon btn-add-cart" data-book-id="{{ $book->id }}" aria-label="Add to cart">
                <i class="fas fa-shopping-bag"></i>
            </button>
        </div>
    </div>
</div>