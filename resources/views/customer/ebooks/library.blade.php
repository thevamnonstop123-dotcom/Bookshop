@extends('layouts.customer')

@section('title', 'My Library — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/library.css') }}">
@endpush

@section('content')

<div class="library-page">
    <div class="container">

        {{-- Header --}}
        <div class="library-header">
            <div class="library-header-left">
                <h1 class="library-title">My Library</h1>
                <p class="library-subtitle">Your purchased e-books — read anytime, anywhere</p>
            </div>
            @if($purchasedBooks->count() > 0)
                <a href="{{ route('books.index') }}" class="library-browse-btn">
                    <i class="fas fa-book-open"></i> Browse More Books
                </a>
            @endif
        </div>

        {{-- Book Grid --}}
        @if($purchasedBooks->count() > 0)
            <div class="library-grid">
                @foreach($purchasedBooks as $book)
                    <div class="library-book-card">
                        <a href="{{ route('customer.ebooks.read', $book) }}" target="_blank" class="library-book-cover-link">
                            <div class="library-book-cover-frame">
                                <img src="{{ $book->image && $book->image !== 'default.png' ? asset('storage/'.$book->image) : 'https://placehold.co/400x560/0F172A/FFFFFF?text='.urlencode($book->title) }}"
                                     alt="{{ $book->title }}"
                                     class="library-book-cover"
                                     loading="lazy">
                                <div class="library-book-overlay">
                                    <span class="library-book-overlay-text">
                                        <i class="fas fa-book-open"></i> Read Now
                                    </span>
                                </div>
                            </div>
                        </a>
                        <div class="library-book-body">
                            <div class="library-book-format">
                                <i class="fas fa-bolt"></i> E-Book
                            </div>
                            <h3 class="library-book-title">
                                <a href="{{ route('customer.ebooks.read', $book) }}" target="_blank">{{ $book->title }}</a>
                            </h3>
                            <p class="library-book-author">{{ $book->authors->first()->name ?? 'Unknown Author' }}</p>
                            <div class="library-book-actions">
                                <a href="{{ route('customer.ebooks.read', $book) }}" target="_blank" class="library-action-btn library-action-read">
                                    <i class="fas fa-book-reader"></i> Read
                                </a>
                                <a href="{{ route('customer.ebooks.download', $book) }}" class="library-action-btn library-action-download" title="Download e-book">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="library-empty">
                <div class="library-empty-icon">
                    <i class="fas fa-bookmark"></i>
                </div>
                <h3 class="library-empty-title">No e-books yet</h3>
                <p class="library-empty-message">
                    Your library is waiting to be filled. Purchase e-books from our store and they will appear here instantly.
                </p>
                <a href="{{ route('books.index') }}" class="library-empty-btn">
                    <i class="fas fa-book-open"></i> Browse Books
                </a>
            </div>
        @endif

    </div>
</div>

@endsection