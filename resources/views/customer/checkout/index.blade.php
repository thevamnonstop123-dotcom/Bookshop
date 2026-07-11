@extends('layouts.customer')

@section('title', 'Checkout — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/checkout.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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

            {{-- Step Indicator (2 Steps) --}}
            <div class="checkout-steps">
                <div class="step active" data-step="1">
                    <span class="step-number">1</span>
                    <span class="step-label">Address</span>
                </div>
                <div class="step" data-step="2">
                    <span class="step-number">2</span>
                    <span class="step-label">Payment &amp; Confirm</span>
                </div>
            </div>

            <div class="checkout-layout">

                {{-- LEFT SIDE --}}
                <div class="checkout-main">
                   {{-- Address Dropdown --}}
                    <div class="address-select-wrapper">
                        <div class="address-option" id="addressOption">
                            <button type="button" class="address-select-btn" id="addressSelectBtn">
                                <span class="address-select-label">
                                    <i class="fas fa-map-pin"></i>
                                    <span id="addressDisplayText">Select an address</span>
                                </span>
                                <i class="fas fa-chevron-down"></i>
                            </button>

                            {{-- Dropdown --}}
                            <div class="address-dropdown" id="addressDropdown">
                                <div class="address-dropdown-search">
                                    <i class="fas fa-search"></i>
                                    <input type="text" placeholder="Search addresses..." id="addressSearchInput">
                                </div>

                                {{-- GPS Option --}}
                                <div class="address-option-item gps-option" id="gpsOptionBtn" onclick="detectLocation()">
                                    <div class="address-option-icon">
                                        <i class="fas fa-location-crosshairs"></i>
                                    </div>
                                    <div class="address-option-content">
                                        <span class="address-option-title" id="gpsStatusText">Use Current Location</span>
                                        <span class="address-option-desc" id="gpsStatusDesc">Auto-detect via GPS</span>
                                    </div>
                                    <div class="address-option-spinner" id="gpsSpinner" style="display:none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </div>

                                {{-- GPS Result --}}
                                <div class="address-option-item gps-result-item" id="gpsResultItem" style="display:none;">
                                    <div class="address-option-icon" style="background:#dcfce7;color:#16a34a;">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="address-option-content">
                                        <span class="address-option-title" id="gpsResultTitle">Location Detected</span>
                                        <span class="address-option-desc" id="gpsResultDesc">Tap to use this address</span>
                                    </div>
                                    <span class="address-option-check"><i class="fas fa-check-circle"></i></span>
                                </div>

                                {{-- Saved Addresses --}}
                                @if ($addresses->count() > 0)
                                    <div class="address-dropdown-divider">Saved Addresses</div>
                                    @foreach ($addresses as $address)
                                        <label class="address-option-item address-saved {{ $loop->first ? 'selected' : '' }}" 
                                            data-address-id="{{ $address->id }}"
                                            data-name="{{ $address->receiver_name }}"
                                            data-phone="{{ $address->phone_number }}"
                                            data-address="{{ $address->address_line }}">
                                            <input type="radio" name="address_id" value="{{ $address->id }}" {{ $loop->first ? 'checked' : '' }}>
                                            <div class="address-option-content">
                                                <span class="address-option-title">{{ $address->receiver_name }}</span>
                                                <span class="address-option-desc">{{ Str::limit($address->address_line, 40) }}</span>
                                            </div>
                                            <span class="address-option-check"><i class="fas fa-check-circle"></i></span>
                                        </label>
                                    @endforeach
                                @endif

                                {{-- Manual Entry --}}
                                <div class="address-dropdown-divider">New Address</div>
                                <div class="address-option-item address-new" onclick="toggleNewAddress()">
                                    <div class="address-option-icon">
                                        <i class="fas fa-plus-circle"></i>
                                    </div>
                                    <div class="address-option-content">
                                        <span class="address-option-title">Enter Manually</span>
                                        <span class="address-option-desc">Add a new shipping address</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Address Form (Always visible, filled when address is selected) --}}
                        <div class="address-form" id="addressForm">
                            <div class="address-form-grid">
                                <div class="form-group">
                                    <label class="form-label">Receiver Name <span class="required">*</span></label>
                                    <input type="text" name="receiver_name" class="form-control" 
                                        placeholder="Enter full name" id="receiverName"
                                        value="{{ $addresses->first()->receiver_name ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Phone Number <span class="required">*</span></label>
                                    <input type="tel" name="phone_number" class="form-control" 
                                        placeholder="09xxxxxxxxx" id="phoneNumber" maxlength="11"
                                        value="{{ $addresses->first()->phone_number ?? '' }}">
                                </div>
                                <div class="form-group-full">
                                    <label class="form-label">Address <span class="required">*</span></label>
                                    <textarea name="address_line" class="form-control form-textarea" 
                                            placeholder="Street, City, Region" rows="2" id="addressLine">{{ $addresses->first()->address_line ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- GPS Error --}}
                        <div class="gps-error-msg" id="gpsErrorMsg" style="display:none;">
                            <i class="fas fa-exclamation-circle"></i>
                            <span id="gpsErrorText"></span>
                        </div>
                    </div>

                    {{-- ================================ --}}
                    {{-- STEP 2: PAYMENT & CONFIRM        --}}
                    {{-- ================================ --}}
                    <div class="checkout-step" data-step="2">
                        <div class="checkout-card">
                            <div class="checkout-card-header">
                                <h3 class="checkout-card-title">Payment Method</h3>
                            </div>

                            {{-- Payment Grid --}}
                            <div class="payment-grid">
                                <label class="payment-card selected">
                                    <input type="radio" name="payment_method" value="stripe" checked>
                                    <div class="payment-card-icon stripe">
                                        <i class="far fa-credit-card"></i>
                                    </div>
                                    <div class="payment-card-info">
                                        <span class="payment-card-name">Credit / Debit</span>
                                        <span class="payment-card-desc">Secure via Stripe</span>
                                    </div>
                                    <span class="payment-card-check"><i class="fas fa-check-circle"></i></span>
                                </label>

                                <label class="payment-card">
                                    <input type="radio" name="payment_method" value="kpay">
                                    <div class="payment-card-icon kpay">
                                        <img src="{{ asset('paymentLogo/kbzpay.png') }}" alt="KBZ Pay Logo" class="payment-logo">
                                    </div>
                                    <div class="payment-card-info">
                                        <span class="payment-card-name">KBZ Pay</span>
                                        <span class="payment-card-desc">Mobile wallet</span>
                                    </div>
                                    <span class="payment-card-check"><i class="fas fa-check-circle"></i></span>
                                </label>

                                <label class="payment-card">
                                    <input type="radio" name="payment_method" value="wave">
                                    <div class="payment-card-icon wave">
                                        <img src="{{ asset('paymentLogo/wavepay.png') }}" alt="Wave Pay Logo" class="payment-logo">
                                    </div>
                                    <div class="payment-card-info">
                                        <span class="payment-card-name">Wave Pay</span>
                                        <span class="payment-card-desc">Mobile wallet</span>
                                    </div>
                                    <span class="payment-card-check"><i class="fas fa-check-circle"></i></span>
                                </label>

                                <label class="payment-card">
                                    <input type="radio" name="payment_method" value="cod">
                                    <div class="payment-card-icon cod">
                                        <i class="fas fa-hand-holding-dollar"></i>
                                    </div>
                                    <div class="payment-card-info">
                                        <span class="payment-card-name">Cash on Delivery</span>
                                        <span class="payment-card-desc">Pay on arrival</span>
                                    </div>
                                    <span class="payment-card-check"><i class="fas fa-check-circle"></i></span>
                                </label>
                            </div>

                            {{-- Trust Badges --}}
                            <div class="payment-trust">
                                <span><i class="fas fa-lock"></i> SSL Encrypted</span>
                                <span><i class="fas fa-shield-alt"></i> Secure</span>
                                <span><i class="fas fa-headset"></i> 24/7 Support</span>
                            </div>

                            {{-- ========================================== --}}
                            {{-- CONFIRM — Address + Place Order --}}
                            <div class="confirm-section">
                                <div class="confirm-address-display" id="confirmAddressDisplay">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span id="confirmAddressText">{{ $addresses->first()->receiver_name ?? "No address" }} • {{ $addresses->first()->address_line ?? "No address selected" }}</span>
                                </div>
                                <button type="submit" class="checkout-submit-btn">
                                    <i class="fas fa-lock"></i> Place Order • {{ number_format($total) }} MMK
                                </button>
                            </div>

                            {{-- Navigation --}}
                            <div class="checkout-nav">
                                <button type="button" id="backToStep1" class="btn-ghost">
                                    <i class="fas fa-arrow-left"></i> Back
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ================================ --}}
                {{-- RIGHT SIDEBAR (Only summary)      --}}
                {{-- ================================ --}}
                <div class="checkout-sidebar">
                    <div class="checkout-summary">
                        <h4 class="summary-title">Order Summary</h4>

                        @foreach ($cart->items as $item)
                            @php $price = $item->book->getPriceForFormat($item->format ?? "physical"); @endphp
                            <div class="summary-item">
                                <img src="{{ $item->book->image && $item->book->image !== 'default.png' ? asset('storage/'.$item->book->image) : 'https://placehold.co/48x64/F1F5F9/1E3A8A?text='.urlencode($item->book->title) }}" alt="{{ $item->book->title }}" class="summary-item-img">
                                <span class="summary-item-title">{{ Str::limit($item->book->title, 20) }}</span>
                                <span class="summary-item-qty">×{{ $item->quantity }}</span>
                                <span class="summary-item-price">{{ number_format($price * $item->quantity) }} MMK</span>
                            </div>
                        @endforeach

                        @if($savings > 0)
                            <div class="summary-savings">
                                <i class="fas fa-tag"></i> Save {{ number_format($savings) }} MMK
                            </div>
                        @endif

                        <div class="summary-total">
                            <span>Total</span>
                            <span>{{ number_format($total) }} MMK</span>
                        </div>

                        <div class="summary-secure">
                            <i class="fas fa-lock"></i> Secure checkout
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- Hidden inputs for GPS data --}}
<input type="hidden" name="gps_lat" id="gpsLat">
<input type="hidden" name="gps_lng" id="gpsLng">
<input type="hidden" name="gps_address" id="gpsAddressInput">

@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/customer/checkout.js') }}"></script>
@endpush