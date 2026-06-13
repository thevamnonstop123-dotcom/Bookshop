@extends('layouts.customer')

@section('title', 'My Profile — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/profile.css') }}">
@endpush

@section('content')

<div class="profile-page">
    <div class="container">

        {{-- Profile Banner --}}
        <div class="profile-banner">
            <div class="profile-banner-content">
                <div class="profile-avatar-wrapper">
                    <img src="{{ $customer->image && $customer->image !== 'default.png' ? asset('storage/'.$customer->image) : 'https://ui-avatars.com/api/?name='.urlencode($customer->name).'&background=10B981&color=fff&size=80' }}"
                         alt="{{ $customer->name }}"
                         class="profile-avatar"
                         id="bannerAvatar">
                </div>
                <div class="profile-banner-info">
                    <h1 class="profile-name">{{ $customer->name }}</h1>
                    <p class="profile-meta">
                        <i class="fas fa-envelope"></i> {{ $customer->email }}
                    </p>
                    <p class="profile-meta">
                        <i class="fas fa-calendar"></i> Member since {{ $customer->created_at->format('M Y') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="profile-alert profile-alert-success">
                <i class="fas fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Layout --}}
        <div class="profile-layout">

            {{-- LEFT: Edit Profile --}}
            <div class="profile-main">
                <div class="profile-card">
                    <div class="profile-card-header">
                        <div class="profile-card-icon profile-card-icon-personal">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="profile-card-title">Personal Information</h3>
                    </div>

                    <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
                        @csrf
                        @method('PUT')

                        <div class="profile-form-grid">
                            <div class="profile-form-group">
                                <label for="profileName" class="profile-label">Full Name</label>
                                <div class="profile-input-wrapper">
                                    <i class="fas fa-user profile-input-icon"></i>
                                    <input type="text" id="profileName" name="name" class="profile-input"
                                           value="{{ old('name', $customer->name) }}" required>
                                </div>
                            </div>

                            <div class="profile-form-group">
                                <label for="profileEmail" class="profile-label">Email Address</label>
                                <div class="profile-input-wrapper">
                                    <i class="fas fa-envelope profile-input-icon"></i>
                                    <input type="email" id="profileEmail" name="email" class="profile-input"
                                           value="{{ old('email', $customer->email) }}" required>
                                </div>
                            </div>

                            <div class="profile-form-group">
                                <label for="profilePhone" class="profile-label">Phone Number</label>
                                <div class="profile-input-wrapper">
                                    <i class="fas fa-phone profile-input-icon"></i>
                                    <input type="tel" id="profilePhone" name="phone" class="profile-input"
                                           value="{{ old('phone', $customer->phone) }}" maxlength="11" required
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
                                </div>
                            </div>

                            <div class="profile-form-group">
                                <label for="profileDob" class="profile-label">Date of Birth</label>
                                <div class="profile-input-wrapper">
                                    <i class="fas fa-cake-candles profile-input-icon"></i>
                                    <input type="date" id="profileDob" name="dob" class="profile-input"
                                           value="{{ old('dob', $customer->dob->format('Y-m-d')) }}" required>
                                </div>
                            </div>

                            <div class="profile-form-group">
                                <label for="profileGender" class="profile-label">Gender</label>
                                <select id="profileGender" name="gender" class="profile-input profile-select">
                                    <option value="male" {{ $customer->gender == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $customer->gender == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>

                            <div class="profile-form-group">
                                <label for="profilePassword" class="profile-label">New Password</label>
                                <div class="profile-input-wrapper">
                                    <i class="fas fa-lock profile-input-icon"></i>
                                    <input type="password" id="profilePassword" name="password" class="profile-input"
                                           placeholder="Leave blank to keep current">
                                </div>
                            </div>

                            <div class="profile-form-group profile-form-group-full">
                                <label for="profileImage" class="profile-label">Profile Photo</label>
                                <div class="profile-image-upload">
                                    <img src="{{ $customer->image && $customer->image !== 'default.png' ? asset('storage/'.$customer->image) : 'https://ui-avatars.com/api/?name='.urlencode($customer->name).'&background=10B981&color=fff&size=96' }}"
                                         alt="Avatar preview" class="profile-image-preview" id="avatarPreview">
                                    <div class="profile-image-input-group">
                                        <label for="profileImage" class="profile-image-upload-btn">
                                            <i class="fas fa-camera"></i> Choose Photo
                                        </label>
                                        <input type="file" id="profileImage" name="image" class="profile-image-input-hidden"
                                               accept=".jpg,.jpeg,.png" onchange="previewAvatar(event)">
                                        <span class="profile-image-hint">JPG, JPEG or PNG. Max 2MB.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="profile-save-btn">
                            <i class="fas fa-check"></i> Update Profile
                        </button>
                    </form>
                </div>
            </div>

            {{-- RIGHT: Addresses --}}
            <div class="profile-sidebar">
                <div class="profile-card">
                    <div class="profile-card-header">
                        <div class="profile-card-icon profile-card-icon-address">
                            <i class="fas fa-location-dot"></i>
                        </div>
                        <h3 class="profile-card-title">My Addresses</h3>
                    </div>

                    @forelse($addresses as $address)
                        <div class="address-card {{ $address->is_default ? 'address-card-default' : '' }}">
                            @if($address->is_default)
                                <span class="address-default-tag">
                                    <i class="fas fa-star"></i> Default
                                </span>
                            @endif
                            <div class="address-card-name">{{ $address->receiver_name }}</div>
                            <div class="address-card-phone">{{ $address->phone_number }}</div>
                            <div class="address-card-line">{{ $address->address_line }}</div>
                            <div class="address-card-actions">
                                <button type="button" class="address-action-btn address-action-edit"
                                        data-id="{{ $address->id }}"
                                        data-name="{{ $address->receiver_name }}"
                                        data-phone="{{ $address->phone_number }}"
                                        data-line="{{ $address->address_line }}">
                                    <i class="fas fa-pen-to-square"></i>
                                </button>
                                @if(!$address->is_default)
                                    <form action="{{ route('customer.address.default', $address->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="address-action-btn address-action-default" title="Set as default">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('customer.address.delete', $address->id) }}" method="POST"
                                      onsubmit="return confirm('Delete this address?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="address-action-btn address-action-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="address-empty">
                            <div class="address-empty-icon">
                                <i class="fas fa-map-pin"></i>
                            </div>
                            <p>No addresses saved yet.</p>
                        </div>
                    @endforelse

                    {{-- Add / Edit Address Form --}}
                    <div class="address-form-section" id="addressFormSection">
                        <h4 class="address-form-title" id="addressFormTitle">Add New Address</h4>

                        <form action="{{ route('customer.address.store') }}" method="POST" id="addressForm" class="address-form">
                            @csrf
                            <input type="hidden" name="_method" id="addressMethod" value="POST">

                            <div class="profile-form-group">
                                <label for="addrName" class="profile-label">Receiver Name</label>
                                <input type="text" id="addrName" name="receiver_name" class="profile-input" required
                                       placeholder="Full name of receiver">
                            </div>

                            <div class="profile-form-group">
                                <label for="addrPhone" class="profile-label">Phone Number</label>
                                <input type="tel" id="addrPhone" name="phone_number" class="profile-input"
                                       maxlength="11" required placeholder="09123456789"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
                            </div>

                            <div class="profile-form-group">
                                <label for="addrLine" class="profile-label">Address</label>
                                <textarea id="addrLine" name="address_line" class="profile-input profile-textarea"
                                          rows="3" required placeholder="Street, City, Region"></textarea>
                            </div>

                            <div class="address-form-actions">
                                <button type="submit" class="profile-save-btn profile-save-btn-sm" id="addressSubmitBtn">
                                    <i class="fas fa-plus"></i> Add Address
                                </button>
                                <button type="button" class="address-cancel-btn" id="addressCancelBtn" style="display: none;">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/customer/profile.js') }}"></script>
@endpush