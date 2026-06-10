@extends('layouts.customer')

@section('title', 'My Library - Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/books.css') }}">
@endpush

@section('content')

<div class="books-page">
    <div class="container">
        <h1 style="font-size:26px;font-weight:800;margin-bottom:8px;">My Library</h1>
        <p style="color:var(--color-text-muted);margin-bottom:28px;">Your purchased e-books — read anytime</p>

        @if($purchasedBooks->count() > 0)
            <div class="book-grid">
                @foreach($purchasedBooks as $book)
                    <div class="book-card">
                        <div class="book-cover-wrap">
                            <img src="{{ $book->image && $book->image !== 'default.png' ? asset('storage/'.$book->image) : 'https://placehold.co/400x560/1e293b/f59e0b?text='.urlencode($book->title) }}"
                                 alt="{{ $book->title }}" class="book-cover">
                        </div>
                        <div class="book-info">
                            <div class="book-category" style="color:#10b981;">📱 E-Book</div>
                            <div class="book-title">{{ $book->title }}</div>
                            <div class="book-author">{{ $book->authors->first()->name ?? 'Unknown' }}</div>
                            <div style="display:flex;gap:8px;margin-top:10px;">
                                <a href="{{ route('customer.ebooks.read', $book) }}" target="_blank" class="btn btn-accent btn-sm" style="flex:1;">
                                    <i class="fas fa-book-open"></i> Read
                                </a>
                                <a href="{{ route('customer.ebooks.download', $book) }}" class="btn btn-outline btn-sm">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align:center;padding:80px 20px;color:var(--color-text-muted);">
                <i class="fas fa-tablet-alt" style="font-size:56px;display:block;margin-bottom:20px;opacity:0.3;"></i>
                <h3>No e-books yet</h3>
                <p>Purchase e-books from our store and they'll appear here.</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary mt-20">Browse Books</a>
            </div>
        @endif
    </div>
</div>
@endsection
