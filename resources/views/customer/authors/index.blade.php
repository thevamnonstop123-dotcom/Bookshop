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
                    <div class="author-card-ring"></div>
                </div>

                {{-- Info --}}
                <div class="author-card-info">

                    {{-- Name --}}
                    <h3 class="author-card-name">
                        {{ $author->name }}
                    </h3>

                    {{-- Meta --}}
                    <div class="author-card-meta">

                        {{-- Country (FIXED) --}}
                        @if($author->country)
                            <span class="author-card-meta-item">
                                <i class="fas fa-globe-asia"></i>
                                {{ $author->country->name }}
                            </span>
                        @endif

                        {{-- Books --}}
                        <span class="author-card-meta-item">
                            <i class="fas fa-book"></i>
                            {{ $author->books_count }} {{ \Illuminate\Support\Str::plural('Book', $author->books_count) }}
                        </span>

                    </div>

                </div>

                {{-- CTA --}}
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