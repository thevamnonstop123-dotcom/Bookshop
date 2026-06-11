@extends('layouts.customer')

@section('title', 'Checkout - Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/checkout.css') }}">
    <style>
        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--color-border);
        }

        .payment-methods h4 {
            font-size: 14px;
            font-weight: var(--weight-semibold);
            color: var(--color-text);
            margin-bottom: 4px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
            border: 2px solid var(--color-border);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition-fast);
        }

        .payment-option:hover {
            border-color: var(--color-accent);
        }

        .payment-option.selected {
            border-color: var(--color-accent);
            background-color: #fffbeb;
        }

        .payment-option input[type="radio"] {
            accent-color: var(--color-accent);
            width: 18px;
            height: 18px;
        }

        .payment-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: var(--weight-bold);
            flex-shrink: 0;
        }

        .payment-stripe { background: #635bff; color: #fff; font-size: 14px; }
        .payment-kpay { background: #e8f5e9; color: #2e7d32; font-size: 12px; }
        .payment-wave { background: #e3f2fd; color: #1565c0; font-size: 12px; }
        .payment-cod { background: #fff3e0; color: #e65100; font-size: 12px; }

        .payment-info {
            flex: 1;
        }

        .payment-info .payment-name {
            font-weight: var(--weight-semibold);
            font-size: var(--font-size-sm);
            color: var(--color-text);
        }

        .payment-info .payment-desc {
            font-size: var(--font-size-xs);
            color: var(--color-text-muted);
            margin-top: 2px;
        }

        .btn-place-order {
            width: 100%;
            margin-top: 20px;
            padding: 16px;
            font-size: var(--font-size-md);
            font-weight: var(--weight-semibold);
        }
    </style>
@endpush

@section('content')

<div class="checkout-page">
    <div class="container">

        @if (session('error'))
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                <i class="fas fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
            @csrf

            <div class="checkout-layout">

                {{-- Shipping Address --}}
                <div>
                    <div class="checkout-card">
                        <h3><i class="fas fa-map-marker-alt"></i> Shipping Address</h3>

                        @if ($addresses->count() > 0)
                            <div class="address-list">
                                @foreach ($addresses as $address)
                                    <label class="address-option {{ $loop->first ? 'selected' : '' }}">
                                        <input type="radio" name="address_id" value="{{ $address->id }}"
                                               {{ $loop->first ? 'checked' : '' }}>
                                        <div class="address-detail">
                                            <div class="name">{{ $address->receiver_name }}</div>
                                            <div class="phone">{{ $address->phone_number }}</div>
                                            <div class="address">{{ $address->address_line }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        {{-- New Address Form --}}
                        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--color-border);">
                            <h4 style="font-size: 14px; font-weight: var(--weight-semibold); margin-bottom: 14px; color: var(--color-text);">
                                {{ $addresses->count() > 0 ? 'Or add a new address' : 'Add your shipping address' }}
                            </h4>

                            <div class="form-group">
                                <label class="form-label">Receiver Name</label>
                                <input type="text" name="receiver_name" class="form-control" required
                                       placeholder="Full name of receiver" value="{{ old('receiver_name') }}">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone_number" class="form-control" required
                                       placeholder="09xxxxxxxxx" maxlength="11"
                                       value="{{ old('phone_number') }}"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Address</label>
                                <textarea name="address_line" class="form-control" rows="3" required
                                          placeholder="Street, City, Region">{{ old('address_line') }}</textarea>
                            </div>
                        </div>

                        {{-- Payment Methods --}}
                        <div class="payment-methods">
                            <h4>Payment Method</h4>

                            <label class="payment-option selected">
                                <input type="radio" name="payment_method" value="stripe" checked>
                                <div class="payment-icon payment-stripe">S</div>
                                <div class="payment-info">
                                    <div class="payment-name">Credit/Debit Card (Stripe)</div>
                                    <div class="payment-desc">Pay securely with Visa, Mastercard, or MPU</div>
                                </div>
                            </label>

                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="kpay">
                                <div class="payment-icon payment-kpay">KPay</div>
                                <div class="payment-info">
                                    <div class="payment-name">KBZ Pay</div>
                                    <div class="payment-desc">Pay with your KBZ Pay mobile wallet</div>
                                </div>
                            </label>

                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="wave">
                                <div class="payment-icon payment-wave">Wave</div>
                                <div class="payment-info">
                                    <div class="payment-name">Wave Pay</div>
                                    <div class="payment-desc">Pay with your Wave Pay mobile wallet</div>
                                </div>
                            </label>

                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="cod">
                                <div class="payment-icon payment-cod">COD</div>
                                <div class="payment-info">
                                    <div class="payment-name">Cash on Delivery</div>
                                    <div class="payment-desc">Pay when you receive your order</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Order Summary --}}
                <div>
                    <div class="checkout-card">
                        <h3><i class="fas fa-receipt"></i> Order Summary</h3>

                            @foreach ($cart->items as $item)
                                @if($item->book)
                                    <div class="summary-item">
                                        <img src="{{ $item->book->image ? asset('storage/' . $item->book->image) : 'https://placehold.co/50x68/e2e8f0/64748b?text=Book' }}"
                                            alt="{{ $item->book->title }}">
                                        <div class="summary-item-info">
                                            <div class="summary-item-title">{{ $item->book->title }}</div>
                                            <div class="summary-item-qty">Qty: {{ $item->quantity }}</div>
                                        </div>
                                        <div class="summary-item-price">
                                            @php
                                                $itemPrice = $item->book->isOnSale() ? $item->book->sale_price : $item->book->price;
                                            @endphp
                                            {{ number_format($itemPrice * $item->quantity) }} MMK
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                        <div class="summary-total">
                            <span>Total</span>
                            <span class="summary-total-amount">{{ number_format($total) }} MMK</span>
                        </div>

                        <button type="submit" class="btn btn-accent btn-place-order" id="placeOrderBtn">
                            <i class="fas fa-lock"></i> Place Order
                        </button>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>

@endsection

@push('scripts')
<script>
    <script src="{{ asset('js/customer/cart.js') }}"></script>
    // Payment method selector visual
    document.querySelectorAll('.payment-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;

            // Update button text
            const method = this.querySelector('input').value;
            const btn = document.getElementById('placeOrderBtn');
            if (method === 'stripe') {
                btn.innerHTML = '<i class="fas fa-lock"></i> Pay with Stripe';
            } else if (method === 'kpay') {
                btn.innerHTML = '<i class="fas fa-lock"></i> Pay with KBZ Pay';
            } else if (method === 'wave') {
                btn.innerHTML = '<i class="fas fa-lock"></i> Pay with Wave Pay';
            } else {
                btn.innerHTML = '<i class="fas fa-box"></i> Place Order (COD)';
            }
        });
    });
</script>
@endpush