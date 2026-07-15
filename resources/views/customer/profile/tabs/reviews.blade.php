<div class="profile-card">
    <div class="profile-card-header"><h3 class="profile-card-title">My Reviews</h3></div>
    @if($reviews->count() > 0)
        <div class="profile-reviews-list">
            @foreach($reviews as $review)
                <div class="profile-review-card">
                    <img src="{{ $review->book->image && $review->book->image !== 'default.png' ? asset('storage/'.$review->book->image) : 'https://placehold.co/80x104/F1F5F9/1E3A8A?text='.urlencode(substr($review->book->title,0,2)) }}" alt="{{ $review->book->title }}" class="profile-review-card-cover">
                    <div class="profile-review-card-body">
                        <a href="{{ route('books.show', $review->book->slug) }}" class="profile-review-card-title">{{ $review->book->title }}</a>
                        <div class="profile-review-card-stars">@for($i=1;$i<=5;$i++)<i class="fas fa-star{{ $i <= $review->rating ? '' : '-empty' }}"></i>@endfor<span class="profile-review-card-date">{{ $review->created_at->format('M d, Y') }}</span></div>
                        @if($review->review)<p class="profile-review-card-text">{{ Str::limit($review->review, 120) }}</p>@endif
                        <div class="profile-review-card-actions">
                            <a href="{{ route('books.show', $review->book->slug) }}" class="profile-review-card-link"><img src="{{ asset('mylogo.png') }}" alt="Bookshop" class="brand-logo"> View Book</a>
                            <button class="profile-review-card-link" onclick="editReview({{ $review->id }})"><i class="fas fa-pen"></i> Edit</button>
                            <button class="profile-review-card-link profile-review-card-link-danger" onclick="deleteReview({{ $review->id }})"><i class="fas fa-trash"></i> Delete</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @if($reviews->hasPages())<div class="profile-pagination">{{ $reviews->links('vendor.pagination.default') }}</div>@endif
    @else
        <div class="profile-empty"><i class="fas fa-star-half-stroke"></i><p>No reviews yet.</p></div>
    @endif
</div>
