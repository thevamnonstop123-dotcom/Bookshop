{{--
    Star Rating Input Component
    Usage: <x-customer.star-rating name="rating" :value="4" :readonly="false" />
--}}
@props(['name' => 'rating', 'value' => 0, 'readonly' => false])

<div class="star-rating-input" data-star-input="{{ $name }}">
    @for ($i = 1; $i <= 5; $i++)
        <button type="button"
                class="star-rating-star {{ $i <= $value ? 'active' : '' }}"
                data-star-value="{{ $i }}"
                @if($readonly) disabled @endif
                aria-label="{{ $i }} star{{ $i > 1 ? 's' : '' }}">
            <i class="fas fa-star"></i>
        </button>
    @endfor
    <input type="hidden" name="{{ $name }}" value="{{ $value }}" id="starInput{{ $name }}">
</div>

@if(!$readonly)
    <div class="star-rating-label" data-star-label="{{ $name }}">
        @php
            $labels = [1 => 'Poor', 2 => 'Fair', 3 => 'Good', 4 => 'Great', 5 => 'Excellent'];
        @endphp
        <span>{{ $value > 0 ? $labels[$value] . '!' : 'Tap to rate' }}</span>
    </div>
@endif