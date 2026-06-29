{{--
    Write Review Modal
    Include on book detail and orders pages
--}}

<div class="review-modal-overlay" id="reviewModalOverlay" onclick="closeReviewModal()">
    <div class="review-modal" id="reviewModal" onclick="event.stopPropagation()">
        {{-- Close --}}
        <button class="review-modal-close" onclick="closeReviewModal()" aria-label="Close">
            <i class="fas fa-xmark"></i>
        </button>

        {{-- Header --}}
        <div class="review-modal-header">
            <div class="review-modal-book">
                <img src="{{ $book->image && $book->image !== 'default.png' ? asset('storage/'.$book->image) : 'https://placehold.co/80x104/F1F5F9/1E3A8A?text='.urlencode(substr($book->title,0,2)) }}"
                     alt="{{ $book->title }}"
                     class="review-modal-book-cover">
                <div>
                    <h3 class="review-modal-title">Rate this Book</h3>
                    <p class="review-modal-book-title">{{ $book->title }}</p>
                </div>
            </div>
        </div>

        {{-- Body --}}
        @php
            $ratingValue = isset($existingRating) && $existingRating ? (int) $existingRating->rating : 0;
            $reviewText = isset($existingRating) && $existingRating ? $existingRating->review : '';
        @endphp

        <form id="reviewForm" onsubmit="submitReview(event, {{ $book->id }})">
            {{-- Stars --}}
            <div class="review-modal-stars-section">
                @include('customer.reviews.star-rating', ['name' => 'review_rating', 'value' => $ratingValue])
            </div>

            {{-- Review Text --}}
            <div class="review-modal-text-section" id="reviewTextSection">
                <label class="review-modal-label" for="reviewText">Share your thoughts</label>
                <textarea id="reviewText"
                          name="review"
                          class="review-modal-textarea"
                          placeholder="Tell other readers what you liked about this book..."
                          maxlength="500"
                          rows="4">{{ $reviewText }}</textarea>
                <div class="review-modal-char-count">
                    <span id="reviewCharCount">0</span>/500
                </div>
            </div>

            {{-- Actions --}}
            <div class="review-modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeReviewModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="reviewSubmitBtn" {{ $ratingValue > 0 ? '' : 'disabled' }}>
                    <i class="fas fa-paper-plane"></i>
                    <span>{{ $ratingValue > 0 ? 'Update Review' : 'Submit Review' }}</span>
                    <span class="review-modal-spinner" style="display:none;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>
        </form>

        {{-- Success State --}}
        <div class="review-modal-success" id="reviewSuccess" style="display:none;">
            <div class="review-modal-success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h4>Thank You!</h4>
            <p>Your review has been submitted successfully.</p>
            <button class="btn btn-primary" onclick="closeReviewModal()">Close</button>
        </div>
    </div>
</div>