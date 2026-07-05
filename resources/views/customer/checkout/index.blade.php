@extends('layouts.customer')

@section('title', 'Checkout — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/checkout.css') }}">
@endpush

@section('content')

<div class="checkout-page">
    <div class="container">

        {{-- Breadcrumb --}}
        <nav class="checkout-breadcrumb">
            <a href="{{ route('customer.home') }}">Home</a>
            <i class="fas fa-chevron-right"></i>
            <a href="#" onclick="Cart.open(); return false;">Cart</a>
            <i class="fas fa-chevron-right"></i>
            <span>Checkout</span>
        </nav>

        @if (session('error'))
            <div class="checkout-alert checkout-alert-error">
                <i class="fas fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
            @csrf

            {{-- STEP INDICATOR --}}
            <div class="checkout-steps-indicator">
                <div class="step active" data-step-indicator="1">1. Address</div>
                <div class="step" data-step-indicator="2">2. Payment</div>
                <div class="step" data-step-indicator="3">3. Confirm</div>
            </div>

            <div class="checkout-layout">

                {{-- LEFT SIDE --}}
                <div class="checkout-main">

                    {{-- STEP 1: ADDRESS --}}
                    <div class="checkout-step active" data-step="1">
                        <div class="checkout-card">
                            <div class="checkout-card-header">
                                <div class="checkout-card-icon"><i class="fas fa-truck"></i></div>
                                <div>
                                    <h3 class="checkout-card-title">Shipping Address</h3>
                                    <p class="checkout-card-subtitle">Select a saved address or enter a new one</p>
                                </div>
                            </div>

                            {{-- Saved Addresses --}}
                            @if ($addresses->count() > 0)
                                <div class="address-saved-list">
                                    @foreach ($addresses as $address)
                                        <label class="address-card {{ $loop->first ? 'address-card-selected' : '' }}">
                                            <input type="radio" name="address_id" value="{{ $address->id }}" 
                                                {{ $loop->first ? 'checked' : '' }}>
                                            <div class="address-card-body">
                                                <div class="address-card-receiver">{{ $address->receiver_name }}</div>
                                                <div class="address-card-phone">{{ $address->phone_number }}</div>
                                                <div class="address-card-line">{{ $address->address_line }}</div>
                                            </div>
                                            <div class="address-card-check"><i class="fas fa-check-circle"></i></div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif

                            {{-- New Address Form --}}
                            <div class="checkout-section-divider">
                                <span>Or enter a new address</span>
                            </div>

                            <div class="checkout-form-grid">
                                <div class="form-group-full">
                                    <label class="form-label">Receiver Name</label>
                                    <input type="text" name="receiver_name" class="checkout-input" 
                                        placeholder="Enter full name" id="newReceiverName">
                                </div>
                                <div class="form-group-full">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" name="phone_number" class="checkout-input" 
                                        placeholder="09xxxxxxxxx" id="newPhone" maxlength="11" inputmode="numeric" pattern="[0-9]*" autocomplete="tel">
                                </div>
                                <div class="form-group-full">
                                    <label class="form-label">Full Address</label>
                                    <textarea name="address_line" class="checkout-input checkout-textarea" 
                                        placeholder="Street, City, Region" rows="2" id="newAddress"></textarea>
                                </div>
                            </div>
                            
                            <p class="new-address-hint" id="newAddressHint" style="display:none;">
                                <i class="fas fa-info-circle"></i> Fill in the fields above to use a new address
                            </p>
                        </div>
                    </div>

                    {{-- STEP 2: PAYMENT --}}
                    <div class="checkout-step" data-step="2">
                        <div class="checkout-card">
                            <div class="checkout-card-header">
                                <div class="checkout-card-icon"><i class="fas fa-credit-card"></i></div>
                                <div>
                                    <h3 class="checkout-card-title">Payment Method</h3>
                                    <p class="checkout-card-subtitle">Choose your preferred payment option</p>
                                </div>
                            </div>

                            <div class="payment-methods">
                                <label class="payment-option selected">
                                    <input type="radio" name="payment_method" value="stripe" checked>
                                    <div class="payment-option-icon"><i class="far fa-credit-card"></i></div>
                                    <div class="payment-option-info">
                                        <span class="payment-option-name">Credit / Debit Card</span>
                                        <span class="payment-option-desc">Pay securely via Stripe</span>
                                    </div>
                                    <div class="payment-option-check"><i class="fas fa-check-circle"></i></div>
                                </label>

                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="kpay">
                                    <div class="payment-option-icon payment-icon-kpay"><i class="fas fa-mobile-alt"></i></div>
                                    <div class="payment-option-info">
                                        <span class="payment-option-name">KBZ Pay</span>
                                        <span class="payment-option-desc">Pay with KBZ Pay mobile wallet</span>
                                    </div>
                                    <div class="payment-option-check"><i class="fas fa-check-circle"></i></div>
                                </label>

                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="wave">
                                    <div class="payment-option-icon payment-icon-wave"><i class="fas fa-wifi"></i></div>
                                    <div class="payment-option-info">
                                        <span class="payment-option-name">Wave Pay</span>
                                        <span class="payment-option-desc">Pay with Wave Pay mobile wallet</span>
                                    </div>
                                    <div class="payment-option-check"><i class="fas fa-check-circle"></i></div>
                                </label>

                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="cod">
                                    <div class="payment-option-icon payment-icon-cod"><i class="fas fa-hand-holding-dollar"></i></div>
                                    <div class="payment-option-info">
                                        <span class="payment-option-name">Cash on Delivery</span>
                                        <span class="payment-option-desc">Pay when you receive your order</span>
                                    </div>
                                    <div class="payment-option-check"><i class="fas fa-check-circle"></i></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 3: CONFIRM --}}
                    <div class="checkout-step" data-step="3">
                        <div class="checkout-card">
                            <div class="checkout-card-header">
                                <div class="checkout-card-icon"><i class="fas fa-clipboard-check"></i></div>
                                <div>
                                    <h3 class="checkout-card-title">Confirm Order</h3>
                                    <p class="checkout-card-subtitle">Review your order before placing it</p>
                                </div>
                            </div>

                            <div class="confirm-summary">
                                @foreach ($cart->items as $item)
                                    @php $price = $item->book->isOnSale() ? $item->book->sale_price : $item->book->price; @endphp
                                    <div class="summary-item">
                                        <div class="summary-item-left">
                                            <span class="summary-item-title">{{ Str::limit($item->book->title, 40) }}</span>
                                            <span class="summary-item-qty">Qty: {{ $item->quantity }}</span>
                                        </div>
                                        <span class="summary-item-price">{{ number_format($price * $item->quantity) }} MMK</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="confirm-address" id="confirmAddress" style="display:none;">
                                <strong><i class="fas fa-map-marker-alt"></i> Shipping to:</strong>
                                <span id="confirmAddressText"></span>
                            </div>

                            <button type="submit" class="checkout-submit-btn">
                                <i class="fas fa-lock"></i> Place Order • {{ number_format($total) }} MMK
                            </button>
                        </div>
                    </div>

                    {{-- NAVIGATION BUTTONS --}}
                    <div class="checkout-step-actions">
                        <button type="button" id="prevStep" class="btn btn-outline" style="display:none;">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="button" id="nextStep" class="checkout-submit-btn">
                            Continue <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                {{-- RIGHT SUMMARY --}}
                <div class="checkout-sidebar">
                    <div class="checkout-card checkout-summary-card">
                        <h3 class="checkout-summary-title">Order Summary</h3>

                        @foreach ($cart->items as $item)
                            @php $price = $item->book->isOnSale() ? $item->book->sale_price : $item->book->price; @endphp
                            <div class="summary-item">
                                <div class="summary-item-left">
                                    <span>{{ Str::limit($item->book->title, 25) }}</span>
                                    <span class="summary-item-qty">x{{ $item->quantity }}</span>
                                </div>
                                <span>{{ number_format($price * $item->quantity) }} MMK</span>
                            </div>
                        @endforeach

                        @if($savings > 0)
                            <div class="summary-savings">
                                <i class="fas fa-tag"></i> You save {{ number_format($savings) }} MMK
                            </div>
                        @endif

                        <div class="summary-total-row">
                            <span>Total</span>
                            <span>{{ number_format($total) }} MMK</span>
                        </div>
                        
                        <div class="summary-secure">
                            <i class="fas fa-lock"></i> Secure checkout • SSL encrypted
                        </div>
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
