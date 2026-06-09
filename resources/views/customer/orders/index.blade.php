@extends('layouts.customer')

@section('title', 'My Orders - Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/orders.css') }}">
@endpush

@section('content')

<div class="orders-page">
    <div class="container">
        <h1>My Orders</h1>

        @if ($orders->count() > 0)
            <div class="orders-list">
                @foreach ($orders as $order)
                    <a href="{{ route('customer.orders.show', $order) }}" style="text-decoration: none; color: inherit;">
                        <div class="order-card">
                            <div class="order-card-header">
                                <span class="order-number">#{{ $order->order_number }}</span>
                                <span class="order-date">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                            </div>
                            <div class="order-card-body">
                                <div class="order-info">
                                    <span class="order-total">{{ number_format($order->total_amount) }} MMK</span>
                                    <span class="order-items-count">{{ $order->items->count() }} item(s)</span>
                                </div>
                                <span class="order-status status-{{ $order->status }}">
                                    @switch($order->status)
                                        @case('pending') <i class="fas fa-clock"></i> @break
                                        @case('processing') <i class="fas fa-spinner"></i> @break
                                        @case('shipped') <i class="fas fa-truck"></i> @break
                                        @case('delivered') <i class="fas fa-check-circle"></i> @break
                                        @case('cancelled') <i class="fas fa-times-circle"></i> @break
                                    @endswitch
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($orders->hasPages())
                <div class="pagination">
                    {{ $orders->links() }}
                </div>
            @endif
        @else
            <div class="orders-empty">
                <i class="fas fa-shopping-bag"></i>
                <h3>No orders yet</h3>
                <p>Start shopping and your orders will appear here.</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary mt-20">Browse Books</a>
            </div>
        @endif

    </div>
</div>

@endsection