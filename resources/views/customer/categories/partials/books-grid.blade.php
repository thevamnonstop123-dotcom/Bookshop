<div class="category-books-grid">
    @forelse($books as $book)
        <x-customer.book-card :book="$book" :wishlistedIds="$wishlistedIds" />
    @empty
        <div class="category-books-empty">
            <i class="fas fa-bookmark"></i>
            <h3>No books found</h3>
            <p>Try adjusting your filters to find what you're looking for.</p>
            <a href="{{ url()->current() }}" class="btn btn-outline">
                <i class="fas fa-rotate-left"></i> Clear Filters
            </a>
        </div>
    @endforelse
</div>

@if($books->hasPages())
    <div class="category-books-pagination">
        {{ $books->appends(request()->query())->links('vendor.pagination.default') }}
    </div>
@endif
