@extends('layouts.customer')

@section('title', 'Authors — Browse by Writer')
@section('page_title', 'Authors')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/author.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/books.css') }}">
@endpush

@section('content')

<section class="authors-listing section">
    <div class="container">

        <div class="section-heading">
            <span class="section-eyebrow">Browse Writers</span>
            <h1 class="section-title">Shop by Author</h1>
            <p class="section-subtitle">Discover books from world-renowned writers.</p>
        </div>

        <div class="authors-listing-grid">

            @forelse($authors as $author)
            <a href="{{ route('authors.show', $author) }}" class="author-card">
                {{-- Avatar --}}
                <div class="author-card-avatar">
                    <img src="{{ $author->avatarUrl }}"
                        alt="{{ $author->name }}"
                        class="author-card-img"
                        loading="lazy">
                </div>

                {{-- Info --}}
                <div class="author-card-info">
                    {{-- Name + Flag inline --}}
                    <h3 class="author-card-name">
                        {{ $author->name }}
                        @if($author->country && $author->country->code)
                            <img src="https://flagcdn.com/24x18/{{ strtolower($author->country->code) }}.png"
                                 alt="{{ $author->country->name }}"
                                 class="author-card-name-flag"
                                 loading="lazy">
                        @endif
                    </h3>

                    <div class="author-card-meta">
                        @if($author->country)
                            <span class="author-card-meta-item">{{ $author->country->name }}</span>
                            <span class="author-card-meta-sep">·</span>
                        @endif
                        <span class="author-card-meta-item">{{ $author->books_count }} {{ Str::plural('Book', $author->books_count) }}</span>
                    </div>
                </div>

                <span class="author-card-arrow">
                    Explore Books <i class="fas fa-arrow-right"></i>
                </span>
            </a>
            @empty
                <div class="authors-listing-empty">
                    <i class="fas fa-user-pen"></i>
                    <p>No authors available at the moment.</p>
                </div>
            @endforelse

        </div>
    </div>
</section>

@endsection
