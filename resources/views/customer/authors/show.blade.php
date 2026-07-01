@extends('layouts.customer')

@section('title', $author->name . ' — Books & Biography')
@section('page_title', $author->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/author.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/books.css') }}">
@endpush

@section('content')

{{-- HERO --}}
<section class="author-hero">
    <div class="container">
        <div class="author-hero-grid">

            {{-- IMAGE --}}
            <div class="author-hero-image-wrapper">
                <img src="{{ $author->avatarUrl }}"
                     alt="{{ $author->name }}"
                     class="author-hero-image"
                     loading="eager">

                <div class="author-hero-ring"></div>
            </div>

            {{-- INFO --}}
            <div class="author-hero-info">

                {{-- Badge --}}
                <span class="author-hero-badge">
                    <i class="fas fa-star"></i>
                    {{ $author->popularityLabel ?? 'Author' }}
                </span>

                {{-- NAME --}}
                <h1 class="author-hero-name">
                    {{ $author->name }}
                </h1>

                {{-- BIO --}}
                <p class="author-hero-bio">
                    {{ $author->bio ?? 'A writer sharing knowledge, stories, and ideas with readers.' }}
                </p>

                {{-- PRIMARY META (CLEANED HIERARCHY) --}}
                <div class="author-hero-meta">

                    <div class="author-hero-stat">
                        <i class="fas fa-book-open"></i>
                        <span><strong>{{ $author->books_count }}</strong> Books</span>
                    </div>

                    {{-- COUNTRY (SAFE STRING, NOT RELATION) --}}
                    @if($author->country)
                        <div class="author-hero-stat">
                            <i class="fas fa-globe-asia"></i>
                            <span>{{ $author->country->name }}</span>
                        </div>
                    @endif

                    {{-- SALES --}}
                    @if(!empty($author->sales_count))
                        <div class="author-hero-stat">
                            <i class="fas fa-chart-line"></i>
                            <span><strong>{{ number_format($author->sales_count) }}+</strong></span>
                        </div>
                    @endif

                </div>

                {{-- GENRE CHIPS (SAFE CHECK) --}}
                @if(isset($author->genres) && $author->genres->count())
                    <div class="author-hero-genres">
                        @foreach($author->genres as $genre)
                            <span class="author-genre-chip">{{ $genre->name }}</span>
                        @endforeach
                    </div>
                @endif

                {{-- WEBSITE (SAFE URL HANDLING) --}}
                @if(!empty($author->website))
                    @php
                        $website = $author->website;
                        if (!str_starts_with($website, 'http')) {
                            $website = 'https://' . $website;
                        }
                    @endphp

                    <a href="{{ $website }}"
                       target="_blank"
                       rel="noopener"
                       class="author-website-link">
                        <i class="fas fa-globe"></i> Visit Website
                    </a>
                @endif

            </div>
        </div>
    </div>
</section>

{{-- BOOKS --}}
<section class="author-books-section section">
    <div class="container">

        <div class="section-heading-row">
            <div class="heading-text-group">
                <span class="section-eyebrow">Bibliography</span>
                <h2 class="section-title">Books by {{ $author->name }}</h2>
            </div>

            {{-- SORT --}}
            <div class="author-sort-wrapper">
                <select class="author-sort-select"
                        id="authorSortSelect"
                        onchange="window.sortAuthorBooks(this.value)">

                    <option value="latest" {{ ($filters['sort'] ?? '') === 'latest' ? 'selected' : '' }}>
                        Newest
                    </option>

                    <option value="bestseller" {{ ($filters['sort'] ?? '') === 'bestseller' ? 'selected' : '' }}>
                        Best Selling
                    </option>

                    <option value="rated" {{ ($filters['sort'] ?? '') === 'rated' ? 'selected' : '' }}>
                        Highest Rated
                    </option>

                    <option value="price_asc" {{ ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' }}>
                        Price: Low to High
                    </option>

                    <option value="price_desc" {{ ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' }}>
                        Price: High to Low
                    </option>

                </select>
            </div>
        </div>

        {{-- BOOK GRID --}}
        <div id="authorBooksContainer">
            @include('customer.authors.partials.books-grid')
        </div>

    </div>
</section>

@endsection

@push('scripts')
    <script src="{{ asset('js/customer/author.js') }}"></script>
@endpush