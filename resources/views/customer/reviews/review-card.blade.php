{{--
    Review Card Component
    Usage: <x-customer.review-card :review="$review" />
--}}
@props(['review'])

<div class="review-card" data-review-id="{{ $review->id }}">
    {{-- Header --}}
    <div class="review-card-header">
        <div class="review-card-user">
            <img src="{{ $review->customer->image && $review->customer->image !== 'default.png' ? asset('storage/'.$review->customer->image) : 'https://ui-avatars.com/api/?name='.urlencode($review->customer->name).'&background=1E3A8A&color=fff&size=80' }}"
                 alt="{{ $review->customer->name }}"
                 class="review-card-avatar"
                 loading="lazy">
            <div class="review-card-user-info">
                <span class="review-card-name">{{ $review->customer->name }}</span>
                <span class="review-card-badge">
                    <i class="fas fa-check-circle"></i> Verified Purchase
                </span>
            </div>
        </div>
        <div class="review-card-meta">
            <div class="review-card-stars">
                @for ($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star{{ $i <= $review->rating ? '' : '-empty' }}"></i>
                @endfor
            </div>
            <span class="review-card-date">{{ $review->created_at->format('M d, Y') }}</span>
        </div>
    </div>

    {{-- Body --}}
    @if($review->review)
        <p class="review-card-text">{{ $review->review }}</p>
    @endif

    {{-- Footer --}}
    <div class="review-card-footer">
        <button class="review-card-helpful {{ $review->isHelpfulBy(auth('customer')->id() ?? 0) ? 'active' : '' }}"
                data-review-id="{{ $review->id }}"
                onclick="toggleHelpful({{ $review->id }}, this)">
            <i class="fas fa-thumbs-up"></i>
            <span>Helpful</span>
            <span class="review-card-helpful-count">({{ $review->helpful_count }})</span>
        </button>

        @auth('customer')
            @if(auth('customer')->id() === $review->customer_id)
                <div class="review-card-actions">
                    <button class="review-card-action-btn" onclick="editReview({{ $review->id }})" aria-label="Edit review">
                        <i class="fas fa-pen"></i> Edit
                    </button>
                    <button class="review-card-action-btn review-card-action-delete" onclick="deleteReview({{ $review->id }})" aria-label="Delete review">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            @endif
        @endauth
    </div>
</div>