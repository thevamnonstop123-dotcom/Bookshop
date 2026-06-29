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
                <div class="step active" data-step-indicator="1">Address</div>
                <div class="step" data-step-indicator="2">Payment</div>
                <div class="step" data-step-indicator="3">Confirm</div>
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
                                    <p class="checkout-card-subtitle">Select or add address</p>
                                </div>
                            </div>

                            @if ($addresses->count() > 0)
                                <div class="address-saved-list">
                                    @foreach ($addresses as $address)
                                        <label class="address-card {{ $loop->first ? 'address-card-selected' : '' }}">
                                            <input type="radio" name="address_id"
                                                   value="{{ $address->id }}"
                                                   {{ $loop->first ? 'checked' : '' }}>
                                            <div class="address-card-body">
                                                <div class="address-card-receiver">
                                                    {{ $address->receiver_name }}
                                                </div>
                                                <div class="address-card-phone">
                                                    {{ $address->phone_number }}
                                                </div>
                                                <div class="address-card-line">
                                                    {{ $address->address_line }}
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif

                            <div class="checkout-section-divider">
                                <span>Or add new</span>
                            </div>

                            <div class="checkout-form-grid">
                                <input name="receiver_name" class="checkout-input" placeholder="Receiver name" required>
                                <input name="phone_number" class="checkout-input" placeholder="Phone" required>
                                <textarea name="address_line" class="checkout-input checkout-textarea" placeholder="Address"></textarea>
                            </div>
                        </div>

                    </div>

                    {{-- STEP 2: PAYMENT --}}
                    <div class="checkout-step" data-step="2">

                        <div class="checkout-card">

                            <div class="checkout-card-header">
                                <div class="checkout-card-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div>
                                    <h3 class="checkout-card-title">Payment Method</h3>
                                    <p class="checkout-card-subtitle">Choose secure payment option</p>
                                </div>
                            </div>

                            <div class="payment-methods">

                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="stripe" checked>
                                    <div class="payment-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="payment-option-name">Card (Stripe)</div>
                                </label>

                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="kpay">
                                    <div class="payment-icon payment-kpay">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div class="payment-option-name">KBZ Pay</div>
                                </label>

                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="wave">
                                    <div class="payment-icon payment-wave">
                                        <i class="fas fa-wifi"></i>
                                    </div>
                                    <div class="payment-option-name">Wave Pay</div>
                                </label>

                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="cod">
                                    <div class="payment-icon payment-cod">
                                        <i class="fas fa-hand-holding-dollar"></i>
                                    </div>
                                    <div class="payment-option-name">Cash on Delivery</div>
                                </label>

                            </div>
                        </div>
                    </div>

                    {{-- STEP 3: CONFIRM --}}
                    <div class="checkout-step" data-step="3">

                        <div class="checkout-card">
                            <h3 class="checkout-card-title">Confirm Order</h3>

                            <div class="confirm-summary">
                                @foreach ($cart->items as $item)
                                    <div class="summary-item">
                                        <span>{{ $item->book->title }}</span>
                                        <span>x{{ $item->quantity }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="checkout-submit-btn">
                                <i class="fas fa-lock"></i> Place Order
                            </button>

                        </div>

                    </div>

                    {{-- NAVIGATION --}}
                    <div class="checkout-step-actions">
                        <button type="button" id="prevStep" class="btn btn-outline">Back</button>
                        <button type="button" id="nextStep" class="checkout-submit-btn">Continue</button>
                    </div>

                </div>

                {{-- RIGHT SUMMARY (always visible) --}}
                <div class="checkout-sidebar">
                    <div class="checkout-card checkout-summary-card">

                        <h3>Order Summary</h3>

                        @foreach ($cart->items as $item)
                            <div class="summary-item">
                                <span>{{ $item->book->title }}</span>
                            </div>
                        @endforeach

                        <div class="summary-total-row">
                            <span>Total</span>
                            <span>{{ number_format($total) }} MMK</span>
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