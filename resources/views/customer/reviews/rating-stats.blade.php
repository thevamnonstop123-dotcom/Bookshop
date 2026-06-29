{{--
    Rating Statistics Component
    Usage: <x-customer.rating-stats :book="$book" />
--}}
@props(['book'])

<div class="rating-stats">
    <div class="rating-stats-overall">
        <span class="rating-stats-number">{{ number_format($book->rating, 1) }}</span>
        <div class="rating-stats-stars">
            @for ($i = 1; $i <= 5; $i++)
                <i class="fas fa-star{{ $i <= round($book->rating) ? '' : '-empty' }}"></i>
            @endfor
        </div>
        <span class="rating-stats-total">{{ number_format($book->rating_count) }} {{ Str::plural('Review', $book->rating_count) }}</span>
    </div>

    <div class="rating-stats-bars">
        @php $distribution = $book->ratingDistribution(); @endphp
        @foreach ($distribution as $star => $data)
            <div class="rating-stats-row">
                <span class="rating-stars-label">{{ $star }} <i class="fas fa-star"></i></span>
                <div class="rating-stats-bar-wrapper">
                    <div class="rating-stats-bar" style="width: {{ $data['percentage'] }}%"></div>
                </div>
                <span class="rating-stats-pct">{{ $data['percentage'] }}%</span>
            </div>
        @endforeach
    </div>
</div>