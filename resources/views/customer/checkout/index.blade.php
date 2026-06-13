@extends('layouts.customer')

@section('title', 'Checkout — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/checkout.css') }}">
@endpush

@section('content')

<div class="checkout-page">
    <div class="container">

        {{-- Breadcrumb --}}
        <nav class="checkout-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('customer.home') }}">Home</a>
            <i class="fas fa-chevron-right"></i>
            <a href="#" onclick="Cart.open(); return false;">Cart</a>
            <i class="fas fa-chevron-right"></i>
            <span>Checkout</span>
        </nav>

        {{-- Error Alert --}}
        @if (session('error'))
            <div class="checkout-alert checkout-alert-error">
                <i class="fas fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
            @csrf

            <div class="checkout-layout">

                {{-- LEFT: Shipping & Payment --}}
                <div class="checkout-main">

                    {{-- Shipping Address --}}
                    <div class="checkout-card">
                        <div class="checkout-card-header">
                            <div class="checkout-card-icon checkout-card-icon-shipping">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div>
                                <h3 class="checkout-card-title">Shipping Address</h3>
                                <p class="checkout-card-subtitle">Where should we deliver your order?</p>
                            </div>
                        </div>

                        @if ($addresses->count() > 0)
                            <div class="address-saved-list">
                                @foreach ($addresses as $address)
                                    <label class="address-card {{ $loop->first ? 'address-card-selected' : '' }}">
                                        <input type="radio" name="address_id" value="{{ $address->id }}"
                                               {{ $loop->first ? 'checked' : '' }} class="address-card-radio">
                                        <div class="address-card-indicator">
                                            <div class="address-card-radio-visual"></div>
                                        </div>
                                        <div class="address-card-body">
                                            <div class="address-card-receiver">
                                                <i class="fas fa-user"></i>
                                                {{ $address->receiver_name }}
                                            </div>
                                            <div class="address-card-phone">
                                                <i class="fas fa-phone"></i>
                                                {{ $address->phone_number }}
                                            </div>
                                            <div class="address-card-line">
                                                <i class="fas fa-location-dot"></i>
                                                {{ $address->address_line }}
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        {{-- New Address --}}
                        <div class="checkout-section-divider">
                            <span>{{ $addresses->count() > 0 ? 'Or add a new address' : 'Add your shipping address' }}</span>
                        </div>

                        <div class="checkout-form-grid">
                            <div class="checkout-form-group">
                                <label class="checkout-label" for="receiverName">Receiver Name <span class="required">*</span></label>
                                <input type="text" id="receiverName" name="receiver_name" class="checkout-input"
                                       placeholder="Full name of receiver" value="{{ old('receiver_name') }}" required>
                            </div>

                            <div class="checkout-form-group">
                                <label class="checkout-label" for="phoneNumber">Phone Number <span class="required">*</span></label>
                                <input type="tel" id="phoneNumber" name="phone_number" class="checkout-input"
                                       placeholder="09xxxxxxxxx" maxlength="11"
                                       value="{{ old('phone_number') }}"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)" required>
                            </div>

                            <div class="checkout-form-group checkout-form-group-full">
                                <label class="checkout-label" for="addressLine">Address <span class="required">*</span></label>
                                <textarea id="addressLine" name="address_line" class="checkout-input checkout-textarea"
                                          rows="3" placeholder="Street, City, Region" required>{{ old('address_line') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="checkout-card">
                        <div class="checkout-card-header">
                            <div class="checkout-card-icon checkout-card-icon-payment">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div>
                                <h3 class="checkout-card-title">Payment Method</h3>
                                <p class="checkout-card-subtitle">Choose how you would like to pay</p>
                            </div>
                        </div>

                        <div class="payment-methods" id="paymentMethods">
                            <label class="payment-option payment-option-selected" data-method="stripe">
                                <input type="radio" name="payment_method" value="stripe" checked>
                                <div class="payment-option-icon payment-option-icon-stripe">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="payment-option-info">
                                    <span class="payment-option-name">Credit / Debit Card</span>
                                    <span class="payment-option-desc">Visa, Mastercard, MPU — Powered by Stripe</span>
                                </div>
                                <div class="payment-option-check">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </label>

                            <label class="payment-option" data-method="kpay">
                                <input type="radio" name="payment_method" value="kpay">
                                <div class="payment-option-icon payment-option-icon-kpay">
                                    <span>KPay</span>
                                </div>
                                <div class="payment-option-info">
                                    <span class="payment-option-name">KBZ Pay</span>
                                    <span class="payment-option-desc">Pay with your KBZ Pay mobile wallet</span>
                                </div>
                                <div class="payment-option-check">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </label>

                            <label class="payment-option" data-method="wave">
                                <input type="radio" name="payment_method" value="wave">
                                <div class="payment-option-icon payment-option-icon-wave">
                                    <span>Wave</span>
                                </div>
                                <div class="payment-option-info">
                                    <span class="payment-option-name">Wave Pay</span>
                                    <span class="payment-option-desc">Pay with your Wave Pay mobile wallet</span>
                                </div>
                                <div class="payment-option-check">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </label>

                            <label class="payment-option" data-method="cod">
                                <input type="radio" name="payment_method" value="cod">
                                <div class="payment-option-icon payment-option-icon-cod">
                                    <i class="fas fa-hand-holding-dollar"></i>
                                </div>
                                <div class="payment-option-info">
                                    <span class="payment-option-name">Cash on Delivery</span>
                                    <span class="payment-option-desc">Pay when you receive your order</span>
                                </div>
                                <div class="payment-option-check">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </label>
                        </div>
                    </div>

                </div>

                {{-- RIGHT: Order Summary --}}
                <div class="checkout-sidebar">
                    <div class="checkout-card checkout-summary-card">
                        <div class="checkout-card-header">
                            <div class="checkout-card-icon checkout-card-icon-summary">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div>
                                <h3 class="checkout-card-title">Order Summary</h3>
                                <p class="checkout-card-subtitle">{{ $cart->items->sum('quantity') }} {{ Str::plural('item', $cart->items->sum('quantity')) }}</p>
                            </div>
                        </div>

                        <div class="summary-items">
                            @foreach ($cart->items as $item)
                                @if($item->book)
                                    @php
                                        $itemPrice = $item->book->isOnSale() ? $item->book->sale_price : $item->book->price;
                                    @endphp
                                    <div class="summary-item">
                                        <div class="summary-item-image">
                                            <img src="{{ $item->book->image && $item->book->image !== 'default.png' ? asset('storage/' . $item->book->image) : 'https://placehold.co/56x72/F1F5F9/94A3B8?text=Book' }}"
                                                 alt="{{ $item->book->title }}">
                                            <span class="summary-item-qty-badge">{{ $item->quantity }}</span>
                                        </div>
                                        <div class="summary-item-details">
                                            <span class="summary-item-title">{{ $item->book->title }}</span>
                                            <span class="summary-item-author">{{ $item->book->authors->first()->name ?? '' }}</span>
                                        </div>
                                        <span class="summary-item-price">{{ number_format($itemPrice * $item->quantity) }} MMK</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="summary-divider"></div>

                        <div class="summary-total-row">
                            <span class="summary-total-label">Total</span>
                            <span class="summary-total-amount">{{ number_format($total) }} MMK</span>
                        </div>

                        <button type="submit" class="checkout-submit-btn" id="placeOrderBtn">
                            <i class="fas fa-lock"></i> Place Order
                        </button>

                        <p class="checkout-secure-note">
                            <i class="fas fa-shield-halved"></i>
                            Secured by 256-bit SSL encryption
                        </p>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/customer/checkout.js') }}"></script>
@endpush