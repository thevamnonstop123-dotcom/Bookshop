@extends('layouts.customer')

@section('title', 'Authors — Browse by Writer')
@section('page_title', 'Authors')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/author.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/book-details.css') }}">
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
                    <div class="author-card-avatar">
                        <img src="{{ $author->image && $author->image !== 'default.png' ? asset('storage/'.$author->image) : 'https://placehold.co/200x200/F1F5F9/1E3A8A?text='.urlencode(substr($author->name,0,1)) }}"
                             alt="{{ $author->name }}"
                             class="author-card-img"
                             loading="lazy">
                        <div class="author-card-ring"></div>
                    </div>
                    <div class="author-card-info">
                        <h3 class="author-card-name">{{ $author->name }}</h3>
                        <span class="author-card-books">{{ $author->books_count }} {{ Str::plural('Book', $author->books_count) }}</span>
                    </div>
                    <span class="author-card-arrow">
                        View Books <i class="fas fa-arrow-right"></i>
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