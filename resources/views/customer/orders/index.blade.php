@extends('layouts.customer')

@section('title', 'My Orders — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/orders.css') }}">
@endpush

@section('content')

<div class="orders-page">
    <div class="container">

        {{-- Header --}}
        <div class="orders-header">
            <div class="orders-header-left">
                <h1 class="orders-title">My Orders</h1>
                <p class="orders-subtitle">{{ $orders->total() }} {{ Str::plural('order', $orders->total()) }} placed</p>
            </div>
            @if($orders->count() > 0)
                <a href="{{ route('books.index') }}" class="orders-browse-btn">
                    <img src="{{ asset('mylogo.png') }}" alt="Bookshop" class="brand-logo"> Browse Books
                </a>
            @endif
        </div>

        {{-- Orders List --}}
        @if ($orders->count() > 0)
            <div class="orders-list">
                @foreach ($orders as $order)
                    <a href="{{ route('customer.orders.show', $order) }}" class="order-card-link">
                        <article class="order-card">
                            <div class="order-card-top">
                                <div class="order-card-number">
                                    <i class="fas fa-hashtag"></i>
                                    {{ $order->order_number }}
                                </div>
                                <div class="order-card-date">
                                    <i class="fas fa-calendar"></i>
                                    {{ $order->created_at->format('d M Y') }}
                                </div>
                            </div>

                            <div class="order-card-body">
                                <div class="order-card-info">
                                    <span class="order-card-total">{{ number_format($order->total_amount) }} MMK</span>
                                    <span class="order-card-items">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</span>
                                </div>

                                <span class="order-status-badge order-status-{{ $order->status }}">
                                    @switch($order->status)
                                        @case('pending')
                                            <i class="fas fa-clock"></i>
                                            @break
                                        @case('processing')
                                            <i class="fas fa-spinner fa-spin"></i>
                                            @break
                                        @case('shipped')
                                            <i class="fas fa-truck-fast"></i>
                                            @break
                                        @case('delivered')
                                            <i class="fas fa-circle-check"></i>
                                            @break
                                        @case('cancelled')
                                            <i class="fas fa-circle-xmark"></i>
                                            @break
                                        @default
                                            <i class="fas fa-circle"></i>
                                    @endswitch
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>

                            <div class="order-card-footer">
                                <span class="order-card-view">
                                    View Details <i class="fas fa-chevron-right"></i>
                                </span>
                            </div>
                        </article>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($orders->hasPages())
                <div class="orders-pagination">
                    {{ $orders->links('vendor.pagination.default') }}
                </div>
            @endif
        @else
            <div class="orders-empty">
                <div class="orders-empty-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <h3 class="orders-empty-title">No orders yet</h3>
                <p class="orders-empty-message">
                    You have not placed any orders yet. Start exploring our collection and your orders will appear here.
                </p>
                <a href="{{ route('books.index') }}" class="orders-empty-btn">
                    <img src="{{ asset('mylogo.png') }}" alt="Bookshop" class="brand-logo"> Browse Books
                </a>
            </div>
        @endif

    </div>
</div>

@endsection