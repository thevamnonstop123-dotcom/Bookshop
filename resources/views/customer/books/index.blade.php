@extends('layouts.customer')

@section('title', 'Browse Books — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/books.css') }}">
@endpush

@section('content')
<div class="books-page" id="booksPage">
    <div class="container">
        <div class="books-layout">
            <div class="books-main" id="booksMain">

                <div class="filter-bar" id="filterBar">
                    <div class="filter-bar-scroll">
                        <button class="filter-bar-btn filter-bar-btn-primary" type="button" data-dropdown-type="all">
                            ☰ Filters
                            <span class="filter-bar-badge" id="filterBadge" style="display: none;">0</span>
                        </button>

                        @foreach($filterGroups as $group)
                            <button class="filter-bar-btn {{ $group['isActive'] ? 'is-active' : '' }}" type="button" data-dropdown-type="{{ $group['key'] }}">
                                {{ $group['label'] }} ▾
                            </button>
                        @endforeach

                        <span class="filter-bar-separator"></span>
                        <button class="filter-bar-btn {{ ($filters['sort'] ?? 'featured') !== 'featured' ? 'is-active' : '' }}" 
                                type="button" 
                                data-dropdown-type="sort" 
                                id="bookSortButton">
                            Sort: {{ $sortOptions[$filters['sort'] ?? 'featured'] }} ▾
                        </button>
                    </div>

                    <div class="books-search-bar">
                        <div class="search-input-wrapper">
                            <i class="fas fa-search"></i>
                            <input type="text" id="bookSearchInput" class="search-input" placeholder="Search books..." value="{{ $filters['search'] ?? '' }}">
                            <button type="button" id="searchClearBtn" class="search-clear-btn" style="display: {{ isset($filters['search']) ? 'flex' : 'none' }};">&times;</button>
                        </div>
                    </div>
                </div>

                <div class="active-filters" id="activeFilters">
                    @foreach($activeFilters as $chip)
                        <span class="filter-chip" data-remove="{{ $chip['param'] }}">
                            {{ $chip['label'] }} &times;
                        </span>
                    @endforeach
                    @if(count($activeFilters) > 1)
                        <button class="filter-chip-clear" id="clearAllChips">Clear All</button>
                    @endif
                </div>

                <div class="books-results-bar">
                    <span class="books-count" id="booksCount">{{ $books->total() }} {{ Str::plural('book', $books->total()) }} found</span>
                </div>

                <div id="booksContainer">
                    @include('customer.books.partials.books-grid')
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        window.AppFilterState = {
            categories: @json($categories),
            authors: @json($authors),
            priceRange: @json($priceRange),
            languages: @json($languages),
            sortOptions: @json($sortOptions),
            currentFilters: @json($filters)
        };
    </script>
    <script src="{{ asset('js/customer/books.js') }}"></script>
@endpush