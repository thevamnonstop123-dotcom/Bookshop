@extends('layouts.customer')

@section('title', $author->name . ' — Books & Biography')
@section('page_title', $author->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/author.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/books.css') }}">
@endpush

@section('content')

{{-- Hero Section --}}
<section class="author-hero">
    <div class="container">
        <div class="author-hero-grid">
            {{-- Photo --}}
            <div class="author-hero-image-wrapper">
                <img src="{{ $author->image && $author->image !== 'default.png' ? asset('storage/'.$author->image) : 'https://placehold.co/300x300/F1F5F9/1E3A8A?text='.urlencode(substr($author->name,0,1)) }}"
                     alt="{{ $author->name }}"
                     class="author-hero-image"
                     loading="eager">
                <div class="author-hero-ring"></div>
            </div>

            {{-- Info --}}
            <div class="author-hero-info">
                <span class="author-hero-badge">
                    <i class="fas fa-star"></i> Popular Author
                </span>
                <h1 class="author-hero-name">{{ $author->name }}</h1>
                <p class="author-hero-bio">{{ $author->bio ?? 'A talented author sharing knowledge and stories with the world.' }}</p>

                <div class="author-hero-meta">
                    <div class="author-hero-stat">
                        <i class="fas fa-book-open"></i>
                        <span><strong>{{ $author->books_count }}</strong> Published Books</span>
                    </div>
                    @if($author->country)
                        <div class="author-hero-stat">
                            <i class="fas fa-globe-asia"></i>
                            <span>{{ $author->country }}</span>
                        </div>
                    @endif
                    @if($author->genres)
                        <div class="author-hero-stat">
                            <i class="fas fa-tags"></i>
                            <span>{{ $author->genres }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Books Section --}}
<section class="author-books-section section">
    <div class="container">
        <div class="section-heading-row">
            <div class="heading-text-group">
                <span class="section-eyebrow">Bibliography</span>
                <h2 class="section-title">Books by {{ $author->name }}</h2>
            </div>

            {{-- Sort Dropdown --}}
            <div class="author-sort-wrapper">
                <select class="author-sort-select" id="authorSortSelect" onchange="window.sortAuthorBooks(this.value)">
                    <option value="latest" {{ ($filters['sort'] ?? '') === 'latest' ? 'selected' : '' }}>Newest</option>
                    <option value="bestseller" {{ ($filters['sort'] ?? '') === 'bestseller' ? 'selected' : '' }}>Best Selling</option>
                    <option value="rated" {{ ($filters['sort'] ?? '') === 'rated' ? 'selected' : '' }}>Highest Rated</option>
                    <option value="price_asc" {{ ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                </select>
            </div>
        </div>

        {{-- Books Grid --}}
        <div class="author-books-grid">
            @forelse($books as $book)
                <x-customer.book-card :book="$book" :wishlistedIds="$wishlistedIds" />
            @empty
                <div class="author-books-empty">
                    <i class="fas fa-book-sparkles"></i>
                    <p>No books found for this author yet.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($books->hasPages())
            <div class="author-books-pagination">
                {{ $books->appends(request()->query())->links('vendor.pagination.default') }}
            </div>
        @endif
    </div>
</section>

@endsection

@push('scripts')
    <script src="{{ asset('js/customer/author.js') }}"></script>
@endpush