@extends('layouts.customer')

@section('title', $author->name . ' — Books & Biography')

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
            </div>

            {{-- INFO --}}
            <div class="author-hero-info">

                {{-- Badge --}}
                @if($author->popularityLabel)
                    <span class="author-hero-badge">
                        <i class="fas fa-star"></i> {{ $author->popularityLabel }}
                    </span>
                @endif

                {{-- NAME --}}
                <h1 class="author-hero-name">{{ $author->name }}</h1>

                {{-- META ROW --}}
                <div class="author-hero-meta">
                    {{-- Books count --}}
                    <div class="author-meta-item">
                        <i class="fas fa-book-open"></i>
                        <span><strong>{{ $author->books_count }}</strong> {{ Str::plural('Book', $author->books_count) }}</span>
                    </div>

                    {{-- Country with FLAG --}}
                    @if($author->country)
                        <div class="author-meta-item">
                            @if($author->country->code)
                                <img src="https://flagcdn.com/24x18/{{ strtolower($author->country->code) }}.png"
                                     alt="{{ $author->country->name }}"
                                     class="author-country-flag"
                                     loading="lazy">
                            @else
                                <i class="fas fa-globe-asia"></i>
                            @endif
                            <span>{{ $author->country->name }}</span>
                        </div>
                    @endif

                    {{-- Active years --}}
                    @if($author->activeYears)
                        <div class="author-meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>{{ $author->activeYears }}</span>
                        </div>
                    @endif

                    {{-- Sales --}}
                    @if($author->sales_count > 0)
                        <div class="author-meta-item">
                            <i class="fas fa-chart-line"></i>
                            <span><strong>{{ number_format($author->sales_count) }}+</strong> sold</span>
                        </div>
                    @endif
                </div>

                {{-- BIO --}}
                @if($author->bio)
                    <div class="author-hero-bio">
                        <p>{{ Str::limit($author->bio, 300) }}</p>
                    </div>
                @endif

                {{-- GENRES --}}
                @if(isset($author->genres) && $author->genres->count())
                    <div class="author-hero-genres">
                        @foreach($author->genres as $genre)
                            <span class="author-genre-chip">{{ $genre->name }}</span>
                        @endforeach
                    </div>
                @endif

                {{-- WEBSITE --}}
                @if(!empty($author->website))
                    @php
                        $website = $author->website;
                        if (!str_starts_with($website, 'http')) {
                            $website = 'https://' . $website;
                        }
                    @endphp
                    <a href="{{ $website }}" target="_blank" rel="noopener" class="author-website-link">
                        <i class="fas fa-arrow-up-right-from-square"></i> Visit Website
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
                <span class="section-eyebrow">Books</span>
                <h2 class="section-title">By {{ $author->name }}</h2>
            </div>
            <div class="author-sort-wrapper" id="authorSortWrapper">
                <button class="author-sort-btn" id="authorSortBtn" onclick="toggleAuthorDropdown()" type="button">
                    <span id="authorSortLabel">{{ $sortLabels[$filters['sort'] ?? 'latest'] ?? 'Newest' }}</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="author-sort-dropdown" id="authorSortDropdown" style="display:none;">
                    @foreach($sortLabels as $value => $label)
                        <div class="author-sort-item {{ ($filters['sort'] ?? 'latest') === $value ? 'active' : '' }}" onclick="selectAuthorSort('{{ $value }}', '{{ $label }}')">
                            {{ $label }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div id="authorBooksContainer">
            @include('customer.authors.partials.books-grid')
        </div>
    </div>
</section>

@endsection

@push('scripts')
    <script src="{{ asset('js/customer/author.js') }}"></script>
@endpush
