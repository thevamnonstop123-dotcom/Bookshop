@extends('layouts.customer')

@section('title', 'Browse Books - Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/books.css') }}">
@endpush

@section('content')

<div class="books-page">
    <div class="container">
        <div class="books-layout">

            {{-- Sticky Filters Sidebar --}}
            <aside class="books-filters">

                <div class="filter-card">
                    <h4><i class="fas fa-layer-group"></i> Categories</h4>
                    <ul class="filter-list">
                        <li>
                            <a href="{{ route('books.index', array_merge(request()->except('category'), ['category' => ''])) }}"
                               class="{{ empty(request('category')) ? 'active' : '' }}">
                                All Categories
                                <span class="count">{{ $books->total() }}</span>
                            </a>
                        </li>
                        @foreach ($categories as $cat)
                            <li>
                                <a href="{{ route('books.index', array_merge(request()->all(), ['category' => $cat->id])) }}"
                                   class="{{ request('category') == $cat->id ? 'active' : '' }}">
                                    {{ $cat->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="filter-card">
                    <h4><i class="fas fa-tag"></i> Price Range (MMK)</h4>
                    <form action="{{ route('books.index') }}" method="GET" id="priceFilterForm">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                        <div class="form-group" style="margin-bottom:4px;">
                            <input type="number" name="min_price" class="form-control" placeholder="Min" value="{{ request('min_price') }}" min="0" id="minPrice">
                        </div>
                        <div class="price-separator">to</div>
                        <div class="form-group" style="margin-top:4px;margin-bottom:6px;">
                            <input type="number" name="max_price" class="form-control" placeholder="Max" value="{{ request('max_price') }}" min="0" id="maxPrice">
                        </div>
                        <button type="submit" class="btn btn-outline btn-sm">Apply</button>
                    </form>
                    @if (request('min_price') || request('max_price'))
                        <a href="{{ route('books.index', array_merge(request()->except(['min_price','max_price']))) }}" class="filter-reset">
                            <i class="fas fa-times"></i> Clear Price Filter
                        </a>
                    @endif
                </div>

            </aside>

            {{-- Main Content --}}
            <div class="books-main">

                <div class="books-header">
                    <div>
                        <h2>
                            @if (request('search'))
                                Search: "{{ request('search') }}"
                            @elseif (request('category'))
                                {{ $categories->where('id', request('category'))->first()?->name ?? 'Books' }}
                            @else
                                All Books
                            @endif
                        </h2>
                        <span class="result-count">{{ $books->total() }} book(s) found</span>
                    </div>
                    <form action="{{ route('books.index') }}" method="GET">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                        <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                        <select name="sort" onchange="this.form.submit()">
                            <option value="latest" {{ request('sort')=='latest' ? 'selected' : '' }}>Sort: Latest</option>
                            <option value="price_asc" {{ request('sort')=='price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort')=='price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                    </form>
                </div>

                {{-- Book Grid --}}
                <div class="book-grid">
                    @forelse ($books as $book)
                        <div class="book-card">
                            <a href="{{ route('books.show', $book->slug) }}">
                                <div class="book-cover-wrap">
                                    @if($book->isOnSale())
                                        <span class="sale-badge">-{{ $book->discountPercentage() }}%</span>
                                    @endif
                                    <img src="{{ $book->image && $book->image !== 'default.png' ? asset('storage/'.$book->image) : 'https://placehold.co/400x560/1e293b/f59e0b?text='.urlencode($book->title) }}"
                                         alt="{{ $book->title }}" class="book-cover">
                                </div>
                            </a>
                            <div class="book-info">
                                <div class="book-category">{{ $book->category?->name }}</div>
                                <div class="book-title">{{ $book->title }}</div>
                                <div class="book-author">{{ $book->authors->first()->name ?? 'Unknown' }}</div>
                                <div class="book-footer">
                                    <span class="book-price">
                                        @if($book->isOnSale())
                                            <span class="original">{{ number_format($book->price) }}</span>
                                            <span class="sale">{{ number_format($book->sale_price) }} MMK</span>
                                        @else
                                            {{ number_format($book->price) }} MMK
                                        @endif
                                    </span>
                                    <button class="btn btn-accent btn-sm btn-add-cart" data-book-id="{{ $book->id }}">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:var(--color-text-muted);">
                            <i class="fas fa-search" style="font-size:48px;display:block;margin-bottom:16px;opacity:0.4;"></i>
                            <h3 style="color:var(--color-text);">No books found</h3>
                            <p>Try adjusting your filters or search terms.</p>
                            <a href="{{ route('books.index') }}" class="btn btn-outline btn-sm mt-10">Clear All Filters</a>
                        </div>
                    @endforelse
                </div>

                @if ($books->hasPages())
                    <div class="pagination">
                        {{ $books->appends(request()->all())->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/customer/cart.js') }}"></script>
<script>
    document.getElementById('priceFilterForm')?.addEventListener('submit', function(e) {
        const min = parseInt(document.getElementById('minPrice').value) || 0;
        const max = parseInt(document.getElementById('maxPrice').value) || 0;
        if (min < 0) document.getElementById('minPrice').value = 0;
        if (max < 0) document.getElementById('maxPrice').value = 0;
        if (min > 0 && max > 0 && max < min) {
            e.preventDefault();
            alert('Max price must be greater than Min price.');
        }
    });
</script>
@endpush