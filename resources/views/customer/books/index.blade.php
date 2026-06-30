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
                            <span class="filter-bar-badge" id="filterBadge">0</span>
                        </button>

                        @foreach($filterGroups as $group)
                            <button class="filter-bar-btn {{ $group['isActive'] ? 'is-active' : '' }}" type="button" data-dropdown-type="{{ $group['key'] }}">
                                {{ $group['label'] }} ▾
                            </button>
                        @endforeach

                        <span class="filter-bar-separator"></span>
                        <select class="filter-bar-btn filter-bar-sort" id="bookSortSelect">
                            @foreach($sortOptions as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['sort'] ?? 'featured') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
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

<div id="filterData" style="display:none !important;"
    data-categories='@json($categories)'
    data-authors='@json($authors)'
    data-price-range='@json($priceRange)'
    data-languages='@json($languages)'
    data-current-filters='@json($filters)'
></div>

@endsection

@push('scripts')
    <script src="{{ asset('js/customer/books.js') }}"></script>
@endpush