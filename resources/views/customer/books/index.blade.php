@extends('layouts.customer')

@section('title', 'Browse Books — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/books.css') }}">
@endpush

@section('content')

<div class="books-page">
    <div class="container">
        <div class="books-layout">

            {{-- FILTERS SIDEBAR --}}
            <aside class="books-sidebar" id="booksSidebar">

                {{-- Mobile Filter Header --}}
                <div class="sidebar-mobile-header">
                    <h3><i class="fas fa-sliders"></i> Filters</h3>
                    <button class="sidebar-close" id="sidebarClose" aria-label="Close filters">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Categories --}}
                <div class="filter-block">
                    <h4 class="filter-heading">
                        <i class="fas fa-layer-group"></i> Categories
                    </h4>
                    <ul class="filter-list">
                        <li>
                            <a href="{{ route('books.index', array_merge(request()->except('category'), ['category' => ''])) }}"
                               class="filter-item {{ empty(request('category')) ? 'filter-item-active' : '' }}">
                                <span>All Categories</span>
                                <span class="filter-count">{{ $books->total() }}</span>
                            </a>
                        </li>
                        @foreach ($categories as $cat)
                            <li>
                                <a href="{{ route('books.index', array_merge(request()->all(), ['category' => $cat->id])) }}"
                                   class="filter-item {{ request('category') == $cat->id ? 'filter-item-active' : '' }}">
                                    <span>{{ $cat->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Price Range --}}
                <div class="filter-block">
                    <h4 class="filter-heading">
                        <i class="fas fa-tag"></i> Price Range
                    </h4>
                    <form action="{{ route('books.index') }}" method="GET" id="priceFilterForm" class="filter-price-form">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        <input type="hidden" name="sort" value="{{ request('sort') }}">

                        <div class="price-inputs">
                            <div class="price-input-group">
                                <label for="minPrice" class="price-label">Min</label>
                                <div class="price-input-wrapper">
                                    <span class="price-currency">K</span>
                                    <input type="number" name="min_price" id="minPrice" class="price-input"
                                           placeholder="0" value="{{ request('min_price') }}" min="0">
                                </div>
                            </div>
                            <span class="price-separator">
                                <i class="fas fa-minus"></i>
                            </span>
                            <div class="price-input-group">
                                <label for="maxPrice" class="price-label">Max</label>
                                <div class="price-input-wrapper">
                                    <span class="price-currency">K</span>
                                    <input type="number" name="max_price" id="maxPrice" class="price-input"
                                           placeholder="Any" value="{{ request('max_price') }}" min="0">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="filter-apply-btn">
                            Apply Filter
                        </button>

                        @if (request('min_price') || request('max_price'))
                            <a href="{{ route('books.index', array_merge(request()->except(['min_price', 'max_price']))) }}"
                               class="filter-reset-link">
                                <i class="fas fa-rotate-left"></i> Clear Price
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Active Filters --}}
                @if (request('search') || request('category') || request('min_price') || request('max_price'))
                    <div class="filter-block">
                        <h4 class="filter-heading">
                            <i class="fas fa-filter"></i> Active Filters
                        </h4>
                        <div class="active-filters">
                            @if (request('search'))
                                <a href="{{ route('books.index', array_merge(request()->except('search'))) }}" class="active-filter-tag">
                                    <i class="fas fa-times"></i> "{{ request('search') }}"
                                </a>
                            @endif
                            @if (request('category'))
                                <a href="{{ route('books.index', array_merge(request()->except('category'))) }}" class="active-filter-tag">
                                    <i class="fas fa-times"></i> {{ $categories->where('id', request('category'))->first()?->name ?? 'Category' }}
                                </a>
                            @endif
                            @if (request('min_price') || request('max_price'))
                                <a href="{{ route('books.index', array_merge(request()->except(['min_price', 'max_price']))) }}" class="active-filter-tag">
                                    <i class="fas fa-times"></i>
                                    {{ request('min_price') ? number_format(request('min_price')) : '0' }} — {{ request('max_price') ? number_format(request('max_price')) : 'Any' }} MMK
                                </a>
                            @endif
                            <a href="{{ route('books.index') }}" class="active-filter-clear-all">
                                Clear All
                            </a>
                        </div>
                    </div>
                @endif

            </aside>

            {{-- Mobile Filter Overlay --}}
            <div class="sidebar-overlay" id="sidebarOverlay"></div>

            {{-- MAIN CONTENT --}}
            <div class="books-main">

                {{-- Header --}}
                <div class="books-header">
                    <div class="books-header-left">
                        <h2 class="books-title">
                            @if (request('search'))
                                Results for "{{ request('search') }}"
                            @elseif (request('category'))
                                {{ $categories->where('id', request('category'))->first()?->name ?? 'Books' }}
                            @else
                                All Books
                            @endif
                        </h2>
                        <span class="books-count">{{ $books->total() }} {{ Str::plural('book', $books->total()) }} found</span>
                    </div>

                    <div class="books-header-right">
                        {{-- Mobile Filter Button --}}
                        <button class="btn-filter-toggle" id="filterToggle">
                            <i class="fas fa-sliders"></i> Filters
                            @if (request('search') || request('category') || request('min_price') || request('max_price'))
                                <span class="filter-toggle-badge"></span>
                            @endif
                        </button>

                        {{-- Sort --}}
                        <form action="{{ route('books.index') }}" method="GET" class="sort-form">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="category" value="{{ request('category') }}">
                            <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                            <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                            <div class="sort-select-wrapper">
                                <i class="fas fa-arrow-down-wide-short sort-icon"></i>
                                <select name="sort" class="sort-select" onchange="this.form.submit()">
                                    <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Latest</option>
                                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Book Grid --}}
                @if ($books->count() > 0)
                    <div class="book-grid">
                        @foreach ($books as $book)
                            @include('components.customer.book-card', ['book' => $book])
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h3>No books found</h3>
                        <p>We could not find any books matching your criteria. Try adjusting your filters or search terms.</p>
                        <a href="{{ route('books.index') }}" class="btn btn-outline" style="margin-top:16px;">
                            <i class="fas fa-rotate-left"></i> Clear All Filters
                        </a>
                    </div>
                @endif

                {{-- Pagination --}}
                @if ($books->hasPages())
                    <div class="pagination-wrapper">
                        {{ $books->appends(request()->all())->links('vendor.pagination.default') }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/customer/books.js') }}"></script>
@endpush