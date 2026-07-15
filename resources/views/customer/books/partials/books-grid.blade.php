{{-- Books Grid --}}
<div class="book-grid">
    @forelse($books as $book)
        @include('components.customer.book-card', ['book' => $book])
    @empty
        <div class="empty-state">
            <div class="empty-icon">
                <img src="{{ asset('mylogo.png') }}" alt="Bookshop" class="brand-logo">
            </div>
            <h3>No books found</h3>
            <p>Try adjusting your filters or search terms.</p>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($books->hasPages())
    <div class="pagination-wrapper">
        {{ $books->appends(request()->query())->links('vendor.pagination.default') }}
    </div>
@endif{{-- Filter data for AJAX --}}
<div id="filterData" style="display:none;"
    data-categories='@json($categories ?? [])'
    data-authors='@json($authors ?? [])'
    data-price-range='@json($priceRange ?? ["min" => 0, "max" => 100000])'
></div>
