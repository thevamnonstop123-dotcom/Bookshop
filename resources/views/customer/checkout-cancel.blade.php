@extends('layouts.customer')

@section('title', 'Payment Cancelled - Bookshop')

@section('content')
<div style="min-height: 60vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 40px;">
    <div>
        <div style="width: 80px; height: 80px; background: #fef2f2; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 40px; color: #ef4444; margin-bottom: 24px;">
            <i class="fas fa-times-circle"></i>
        </div>
        <h1 style="font-size: 28px; color: var(--color-text); margin-bottom: 8px;">Payment Cancelled</h1>
        <p style="color: var(--color-text-muted);">Your payment was not processed. You can try again.</p>
        <div style="margin-top: 28px;">
            <a href="{{ route('checkout.index') }}" class="btn btn-primary">Try Again</a>
        </div>
    </div>
</div>
@endsection