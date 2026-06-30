@extends('layouts.customer')

@section('title', 'My Profile — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/profile.css') }}">
@endpush

@section('content')

<div class="profile-page">
    <div class="container">

        <div class="profile-banner">
            <div class="profile-banner-content">
                <div class="profile-avatar-wrapper" id="bannerAvatarWrapper">
                    <img src="{{ $customer->image && $customer->image !== 'default.png' ? asset('storage/'.$customer->image) : 'https://ui-avatars.com/api/?name='.urlencode($customer->name).'&background=1E3A8A&color=fff&size=120' }}"
                         alt="{{ $customer->name }}" class="profile-avatar" id="bannerAvatar">
                </div>
                <div class="profile-banner-info">
                    <h1 class="profile-name">{{ $customer->name }}</h1>
                    <p class="profile-meta"><i class="fas fa-envelope"></i> {{ $customer->email }}</p>
                    <p class="profile-meta"><i class="fas fa-calendar"></i> Member since {{ $customer->created_at->format('M Y') }}</p>
                </div>
            </div>
        </div>

        <div class="profile-tabs-wrapper">
            <div class="profile-tabs">
                <a href="#" class="profile-tab {{ $tab === 'personal' ? 'profile-tab-active' : '' }}" onclick="event.preventDefault();switchProfileTab('personal')">
                    <i class="fas fa-user"></i> Personal
                </a>
                <a href="#" class="profile-tab {{ $tab === 'security' ? 'profile-tab-active' : '' }}" onclick="event.preventDefault();switchProfileTab('security')">
                    <i class="fas fa-shield-halved"></i> Security
                </a>
                <a href="#" class="profile-tab {{ $tab === 'addresses' ? 'profile-tab-active' : '' }}" onclick="event.preventDefault();switchProfileTab('addresses')">
                    <i class="fas fa-location-dot"></i> Addresses
                </a>
                <a href="#" class="profile-tab {{ $tab === 'reviews' ? 'profile-tab-active' : '' }}" onclick="event.preventDefault();switchProfileTab('reviews')">
                    <i class="fas fa-star"></i> Reviews
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="profile-toast profile-toast-success"><i class="fas fa-circle-check"></i> {{ session('success') }}<button onclick="this.parentElement.remove()"><i class="fas fa-xmark"></i></button></div>
        @endif
        @if (session('error'))
            <div class="profile-toast profile-toast-error"><i class="fas fa-circle-exclamation"></i> {{ session('error') }}<button onclick="this.parentElement.remove()"><i class="fas fa-xmark"></i></button></div>
        @endif

        <div class="profile-tab-content">
            @include('customer.profile.tabs.' . $tab)
        </div>

    </div>
</div>

<div class="profile-modal-overlay" id="emailModalOverlay" onclick="closeEmailModal()">
    <div class="profile-modal" onclick="event.stopPropagation()">
        <button class="profile-modal-close" onclick="closeEmailModal()"><i class="fas fa-xmark"></i></button>
        <h3 class="profile-modal-title">Change Email Address</h3>
        <form action="{{ route('customer.profile.email') }}" method="POST" class="profile-modal-form">
            @csrf
            <div class="profile-form-group"><label class="profile-label">Current Password</label><input type="password" name="current_password" class="profile-input" required></div>
            <div class="profile-form-group"><label class="profile-label">New Email</label><input type="email" name="email" class="profile-input" required></div>
            <div class="profile-modal-actions">
                <button type="button" class="profile-btn profile-btn-outline" onclick="closeEmailModal()">Cancel</button>
                <button type="submit" class="profile-btn profile-btn-primary">Update Email</button>
            </div>
        </form>
    </div>
</div>

<div class="profile-modal-overlay" id="addressModalOverlay" onclick="closeAddressModal()">
    <div class="profile-modal" onclick="event.stopPropagation()">
        <button class="profile-modal-close" onclick="closeAddressModal()"><i class="fas fa-xmark"></i></button>
        <h3 class="profile-modal-title" id="addressModalTitle">Add New Address</h3>
        <form action="{{ route('customer.address.store') }}" method="POST" class="profile-modal-form" id="addressModalForm">
            @csrf
            <input type="hidden" name="_method" id="addressModalMethod" value="POST">
            <div class="profile-form-group"><label class="profile-label">Receiver Name</label><input type="text" name="receiver_name" id="addrModalName" class="profile-input" required></div>
            <div class="profile-form-group"><label class="profile-label">Phone Number</label><input type="tel" name="phone_number" id="addrModalPhone" class="profile-input" maxlength="11" required></div>
            <div class="profile-form-group"><label class="profile-label">Address</label><textarea name="address_line" id="addrModalLine" class="profile-input profile-textarea" rows="3" required></textarea></div>
            <div class="profile-modal-actions">
                <button type="button" class="profile-btn profile-btn-outline" onclick="closeAddressModal()">Cancel</button>
                <button type="submit" class="profile-btn profile-btn-primary" id="addressModalSubmitBtn"><i class="fas fa-plus"></i> Add Address</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/customer/profile.js') }}"></script>
@endpush
