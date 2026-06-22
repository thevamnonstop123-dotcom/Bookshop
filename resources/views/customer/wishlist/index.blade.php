@extends('layouts.customer')

@section('title', 'My Wishlist — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/books.css') }}">
@endpush

@section('content')

<div class="books-page">
    <div class="container">
        <div class="section-heading" style="text-align:left;margin-bottom:24px;">
            <h2 style="font-size:24px;font-weight:700;">❤️ My Wishlist</h2>
            <p style="color:#64748B;margin-top:4px;">Books you have saved for later</p>
        </div>

        @if($wishlists->count() > 0)
            <div class="book-grid">
                @foreach($wishlists as $wish)
                    @include('components.customer.book-card', ['book' => $wish->book])
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-heart"></i></div>
                <h3>Your wishlist is empty</h3>
                <p>Start exploring and save books you love!</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary" style="margin-top:16px;">Browse Books</a>
            </div>
        @endif
    </div>
</div>
@endsection
