@extends('layouts.customer')

@section('title', $category->name . ' — ' . $category->books_count . ' Books')
@section('page_title', $category->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/category.css') }}">
@endpush

@section('content')

{{-- Hero Section --}}
<section class="category-hero">
    <div class="container">
        <div class="category-hero-content">
            <span class="category-hero-icon">
                <i class="fas fa-{{ $category->icon ?? 'layer-group' }}"></i>
            </span>
            <h1 class="category-hero-name">{{ $category->name }}</h1>
            <p class="category-hero-count">
                <img src="{{ asset('mylogo.png') }}" alt="Bookshop" class="brand-logo"> {{ $category->books_count }} {{ Str::plural('Book', $category->books_count) }}
            </p>
            @if($category->description)
                <p class="category-hero-desc">{{ $category->description }}</p>
            @endif

            {{-- Popular Authors --}}
            @if($authors->isNotEmpty())
                <div class="category-hero-authors">
                    <span class="category-hero-authors-label">Popular Authors:</span>
                    <div class="category-hero-authors-list">
                        @foreach($authors->take(5) as $author)
                            <a href="{{ route('authors.show', $author) }}" class="category-hero-author-chip">
                                {{ $author->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- Books + Filters --}}
<section class="category-books-section section">
    <div class="container">
        <div class="category-layout">
            {{-- Sidebar Filters --}}
            <aside class="category-filters" id="categoryFilters">
                <div class="category-filters-header">
                    <h3 class="category-filters-title">
                        <i class="fas fa-sliders"></i> Filters
                    </h3>
                    <button class="category-filters-close" onclick="toggleCategoryFilters()" aria-label="Close filters">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>

                <form id="categoryFilterForm" method="GET" action="{{ route('categories.show', $category) }}">
                    {{-- Price Range --}}
                    <div class="category-filter-group">
                        <label class="category-filter-label">Price Range (MMK)</label>
                        <div class="category-price-inputs">
                            <input type="number" name="min_price" class="category-price-input" 
                                   placeholder="{{ number_format($priceRange['min']) }}" 
                                   value="{{ $filters['min_price'] ?? '' }}">
                            <span class="category-price-separator">to</span>
                            <input type="number" name="max_price" class="category-price-input" 
                                   placeholder="{{ number_format($priceRange['max']) }}" 
                                   value="{{ $filters['max_price'] ?? '' }}">
                        </div>
                    </div>

                    {{-- Author Filter --}}
                    @if($authors->isNotEmpty())
                        <div class="category-filter-group">
                            <label class="category-filter-label">Author</label>
                            <select name="author" class="category-filter-select">
                                <option value="">All Authors</option>
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}" {{ ($filters['author'] ?? '') == $author->id ? 'selected' : '' }}>
                                        {{ $author->name }} ({{ $author->books_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Rating Filter --}}
                    <div class="category-filter-group">
                        <label class="category-filter-label">Minimum Rating</label>
                        <select name="rating" class="category-filter-select">
                            <option value="">Any Rating</option>
                            <option value="4" {{ ($filters['rating'] ?? '') == '4' ? 'selected' : '' }}>★★★★☆ 4+ Stars</option>
                            <option value="3" {{ ($filters['rating'] ?? '') == '3' ? 'selected' : '' }}>★★★☆☆ 3+ Stars</option>
                            <option value="2" {{ ($filters['rating'] ?? '') == '2' ? 'selected' : '' }}>★★☆☆☆ 2+ Stars</option>
                        </select>
                    </div>

                    <button type="submit" onclick="event.preventDefault(); document.getElementById(`categoryFilterForm`).dispatchEvent(new Event(`submit`))" class="category-filter-apply">
                        <i class="fas fa-check"></i> Apply Filters
                    </button>

                    @if(!empty(array_filter($filters)))
                        <a href="{{ route('categories.show', $category) }}" class="category-filter-clear">
                            <i class="fas fa-rotate-left"></i> Clear All
                        </a>
                    @endif
                </form>
            </aside>

            {{-- Overlay for mobile --}}
            <div class="category-filters-overlay" id="categoryFiltersOverlay" onclick="toggleCategoryFilters()"></div>

            {{-- Books Content --}}
            <div class="category-books-content">
                <div class="category-books-header">
                    <span class="category-books-found">
                        {{ $books->total() }} {{ Str::plural('book', $books->total()) }} found
                    </span>
                    <div class="category-books-actions">
                        <button class="category-filter-toggle" onclick="toggleCategoryFilters()">
                            <i class="fas fa-sliders"></i> Filters
                        </button>
                        <select class="category-sort-select" name="sort" form="categoryFilterForm" onchange="window.sortCategoryBooks(this.value)">
                            <option value="latest" {{ ($filters['sort'] ?? '') === 'latest' ? 'selected' : '' }}>Newest</option>
                            <option value="popular" {{ ($filters['sort'] ?? '') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                            <option value="bestseller" {{ ($filters['sort'] ?? '') === 'bestseller' ? 'selected' : '' }}>Best Selling</option>
                            <option value="rated" {{ ($filters['sort'] ?? '') === 'rated' ? 'selected' : '' }}>Highest Rated</option>
                            <option value="price_asc" {{ ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' }}>Price: Low → High</option>
                            <option value="price_desc" {{ ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' }}>Price: High → Low</option>
                        </select>
                    </div>
                </div>

                {{-- Books Grid --}}
                <div class="category-books-grid">
                    @forelse($books as $book)
                        <x-customer.book-card :book="$book" :wishlistedIds="$wishlistedIds" />
                    @empty
                        <div class="category-books-empty">
                            <i class="fas fa-bookmark"></i>
                            <h3>No books found</h3>
                            <p>Try adjusting your filters to find what you're looking for.</p>
                            <a href="{{ route('categories.show', $category) }}" class="btn btn-outline">
                                <i class="fas fa-rotate-left"></i> Clear Filters
                            </a>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($books->hasPages())
                    <div class="category-books-pagination">
                        {{ $books->appends(request()->query())->links('vendor.pagination.default') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
    <script src="{{ asset('js/customer/category.js') }}"></script>
@endpush