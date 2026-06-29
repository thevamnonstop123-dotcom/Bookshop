@extends('layouts.customer')

@section('title', 'My Profile — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/profile.css') }}">
@endpush

@section('content')

<div class="profile-page">
    <div class="container">

        {{-- ========== BANNER ========== --}}
        <div class="profile-banner">
            <div class="profile-banner-content">
                <div class="profile-avatar-wrapper" id="bannerAvatarWrapper">
                    <img src="{{ $customer->image && $customer->image !== 'default.png' ? asset('storage/'.$customer->image) : 'https://ui-avatars.com/api/?name='.urlencode($customer->name).'&background=1E3A8A&color=fff&size=120' }}"
                         alt="{{ $customer->name }}"
                         class="profile-avatar"
                         id="bannerAvatar">
                </div>
                <div class="profile-banner-info">
                    <h1 class="profile-name">{{ $customer->name }}</h1>
                    <p class="profile-meta"><i class="fas fa-envelope"></i> {{ $customer->email }}</p>
                    <p class="profile-meta"><i class="fas fa-calendar"></i> Member since {{ $customer->created_at->format('M Y') }}</p>
                </div>
            </div>
        </div>

        {{-- ========== TABS ========== --}}
        <div class="profile-tabs-wrapper">
            <div class="profile-tabs">
                <a href="{{ route('customer.profile', ['tab' => 'personal']) }}" class="profile-tab {{ $tab === 'personal' ? 'profile-tab-active' : '' }}">
                    <i class="fas fa-user"></i> Personal
                </a>
                <a href="{{ route('customer.profile', ['tab' => 'security']) }}" class="profile-tab {{ $tab === 'security' ? 'profile-tab-active' : '' }}">
                    <i class="fas fa-shield-halved"></i> Security
                </a>
                <a href="{{ route('customer.profile', ['tab' => 'addresses']) }}" class="profile-tab {{ $tab === 'addresses' ? 'profile-tab-active' : '' }}">
                    <i class="fas fa-location-dot"></i> Addresses
                </a>
                <a href="{{ route('customer.profile', ['tab' => 'reviews']) }}" class="profile-tab {{ $tab === 'reviews' ? 'profile-tab-active' : '' }}">
                    <i class="fas fa-star"></i> Reviews
                </a>
            </div>
        </div>

        {{-- ========== TOAST ========== --}}
        @if (session('success'))
            <div class="profile-toast profile-toast-success" id="profileToast">
                <i class="fas fa-circle-check"></i> {{ session('success') }}
                <button onclick="this.parentElement.remove()"><i class="fas fa-xmark"></i></button>
            </div>
        @endif
        @if (session('error'))
            <div class="profile-toast profile-toast-error" id="profileToast">
                <i class="fas fa-circle-exclamation"></i> {{ session('error') }}
                <button onclick="this.parentElement.remove()"><i class="fas fa-xmark"></i></button>
            </div>
        @endif

        {{-- ================================================================ --}}
        {{-- TAB: PERSONAL INFORMATION --}}
        {{-- ================================================================ --}}
        @if($tab === 'personal')
        <div class="profile-tab-content">

            {{-- Personal Info Card --}}
            <div class="profile-card">
                <div class="profile-card-header">
                    <h3 class="profile-card-title">Personal Information</h3>
                </div>
                <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
                    @csrf @method('PUT')
                    <div class="profile-form-grid-3">
                        <div class="profile-form-group">
                            <label class="profile-label">Full Name</label>
                            <input type="text" name="name" class="profile-input" value="{{ old('name', $customer->name) }}" required>
                            @error('name') <span class="profile-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="profile-form-group">
                            <label class="profile-label">Phone Number</label>
                            <input type="tel" name="phone" class="profile-input" value="{{ old('phone', $customer->phone) }}" maxlength="11" required
                                   oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)">
                            @error('phone') <span class="profile-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="profile-form-group">
                            <label class="profile-label">Date of Birth</label>
                            <input type="date" name="dob" class="profile-input" value="{{ old('dob', $customer->dob ? $customer->dob->format('Y-m-d') : '') }}" required>
                            @error('dob') <span class="profile-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="profile-form-group">
                            <label class="profile-label">Gender</label>
                            <select name="gender" class="profile-input profile-select" required>
                                <option value="male" {{ old('gender', $customer->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $customer->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender') <span class="profile-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="profile-form-actions">
                        <button type="submit" class="profile-btn profile-btn-primary">
                            <i class="fas fa-check"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>

            {{-- Profile Photo Card --}}
            <div class="profile-card">
                <div class="profile-card-header">
                    <h3 class="profile-card-title">Profile Photo</h3>
                </div>
                <div class="profile-photo-section">
                    <div class="profile-photo-preview" id="photoPreviewZone">
                        <img src="{{ $customer->image && $customer->image !== 'default.png' ? asset('storage/'.$customer->image) : 'https://ui-avatars.com/api/?name='.urlencode($customer->name).'&background=1E3A8A&color=fff&size=200' }}"
                             alt="Photo preview" class="profile-photo-img" id="photoPreview">
                        <div class="profile-photo-overlay" onclick="document.getElementById('photoInput').click()">
                            <i class="fas fa-camera"></i>
                            <span>Change Photo</span>
                        </div>
                    </div>
                    <div class="profile-photo-info">
                        <p class="profile-photo-hint">JPG, PNG or WEBP. Max 2MB.</p>
                        <button class="profile-btn profile-btn-outline" onclick="document.getElementById('photoInput').click()">
                            <i class="fas fa-upload"></i> Upload Photo
                        </button>
                        <input type="file" id="photoInput" accept=".jpg,.jpeg,.png,.webp" onchange="uploadPhoto(event)" style="display:none;">
                    </div>
                </div>
            </div>

        </div>
        @endif

        {{-- ================================================================ --}}
        {{-- TAB: SECURITY --}}
        {{-- ================================================================ --}}
        @if($tab === 'security')
        <div class="profile-tab-content">

            {{-- Email Card --}}
            <div class="profile-card">
                <div class="profile-card-header">
                    <h3 class="profile-card-title">Email Address</h3>
                </div>
                <div class="profile-security-row">
                    <div class="profile-security-info">
                        <span class="profile-security-label">Current Email</span>
                        <span class="profile-security-value">{{ $customer->email }}</span>
                    </div>
                    <button class="profile-btn profile-btn-outline" onclick="openEmailModal()">
                        <i class="fas fa-pen"></i> Change Email
                    </button>
                </div>
            </div>

            {{-- Password Card --}}
            <div class="profile-card">
                <div class="profile-card-header">
                    <h3 class="profile-card-title">Password & Security</h3>
                </div>
                <form action="{{ route('customer.profile.password') }}" method="POST" class="profile-form" id="passwordForm">
                    @csrf

                    {{-- Trick browser into NOT autofilling --}}
                    <input type="text" style="display:none;" autocomplete="username">
                    <input type="password" style="display:none;" autocomplete="current-password">

                    <div class="profile-form-grid-2">
                        <div class="profile-form-group profile-form-group-full">
                            <label class="profile-label">Current Password</label>
                            <div class="profile-input-password">
                                <input type="password" name="current_password" class="profile-input" required
                                    placeholder="Enter current password" autocomplete="new-password">
                                <button type="button" class="profile-password-toggle" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="profile-form-group">
                            <label class="profile-label">New Password</label>
                            <div class="profile-input-password">
                                <input type="password" name="password" class="profile-input" id="newPassword" required minlength="8"
                                    placeholder="Min 8 characters" autocomplete="new-password">
                                <button type="button" class="profile-password-toggle" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="profile-form-group">
                            <label class="profile-label">Confirm Password</label>
                            <div class="profile-input-password">
                                <input type="password" name="password_confirmation" class="profile-input" required
                                    placeholder="Re-enter password" autocomplete="new-password">
                                <button type="button" class="profile-password-toggle" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                    </div>

                    {{-- Password Strength --}}
                    <div class="profile-password-strength" id="passwordStrength" style="display:none;">
                        <div class="profile-strength-bar"><div class="profile-strength-fill" id="strengthFill"></div></div>
                        <ul class="profile-strength-requirements" id="strengthRequirements">
                            <li id="req-length"><i class="fas fa-circle"></i> At least 8 characters</li>
                            <li id="req-uppercase"><i class="fas fa-circle"></i> One uppercase letter</li>
                            <li id="req-number"><i class="fas fa-circle"></i> One number</li>
                            <li id="req-special"><i class="fas fa-circle"></i> One special character</li>
                        </ul>
                    </div>

                    <div class="profile-form-actions">
                        <button type="submit" class="profile-btn profile-btn-primary">
                            <i class="fas fa-lock"></i> Change Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
        @endif

        {{-- ================================================================ --}}
        {{-- TAB: ADDRESSES --}}
        {{-- ================================================================ --}}
        @if($tab === 'addresses')
        <div class="profile-tab-content">
            <div class="profile-card">
                <div class="profile-card-header">
                    <h3 class="profile-card-title">My Addresses</h3>
                    <button class="profile-btn profile-btn-primary profile-btn-sm" onclick="openAddressModal()">
                        <i class="fas fa-plus"></i> Add Address
                    </button>
                </div>

                @forelse($addresses as $address)
                    <div class="profile-address-card {{ $address->is_default ? 'profile-address-card-default' : '' }}">
                        <div class="profile-address-header">
                            <div class="profile-address-receiver">
                                <i class="fas fa-user"></i> {{ $address->receiver_name }}
                            </div>
                            @if($address->is_default)
                                <span class="profile-address-badge">Default</span>
                            @endif
                        </div>
                        <div class="profile-address-phone"><i class="fas fa-phone"></i> {{ $address->phone_number }}</div>
                        <div class="profile-address-line"><i class="fas fa-location-dot"></i> {{ $address->address_line }}</div>
                        <div class="profile-address-actions">
                            <button class="profile-address-btn" onclick="editAddressModal({{ $address->id }}, '{{ $address->receiver_name }}', '{{ $address->phone_number }}', '{{ $address->address_line }}')">
                                <i class="fas fa-pen-to-square"></i> Edit
                            </button>
                            @if(!$address->is_default)
                                <form action="{{ route('customer.address.default', $address->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="profile-address-btn profile-address-btn-default">
                                        <i class="fas fa-star"></i> Set Default
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('customer.address.delete', $address->id) }}" method="POST" onsubmit="return confirm('Delete this address?')" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="profile-address-btn profile-address-btn-delete">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="profile-empty">
                        <i class="fas fa-map-pin"></i>
                        <p>No addresses saved yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
        @endif

        {{-- ================================================================ --}}
        {{-- TAB: REVIEWS --}}
        {{-- ================================================================ --}}
        @if($tab === 'reviews')
        <div class="profile-tab-content">
            <div class="profile-card">
                <div class="profile-card-header">
                    <h3 class="profile-card-title">My Reviews</h3>
                </div>

                @if($reviews->count() > 0)
                    <div class="profile-reviews-list">
                        @foreach($reviews as $review)
                            <div class="profile-review-card">
                                <img src="{{ $review->book->image && $review->book->image !== 'default.png' ? asset('storage/'.$review->book->image) : 'https://placehold.co/80x104/F1F5F9/1E3A8A?text='.urlencode(substr($review->book->title,0,2)) }}"
                                     alt="{{ $review->book->title }}" class="profile-review-card-cover">
                                <div class="profile-review-card-body">
                                    <a href="{{ route('books.show', $review->book->slug) }}" class="profile-review-card-title">{{ $review->book->title }}</a>
                                    <div class="profile-review-card-stars">
                                        @for($i=1;$i<=5;$i++)
                                            <i class="fas fa-star{{ $i <= $review->rating ? '' : '-empty' }}"></i>
                                        @endfor
                                        <span class="profile-review-card-date">{{ $review->created_at->format('M d, Y') }}</span>
                                    </div>
                                    @if($review->review)
                                        <p class="profile-review-card-text">{{ Str::limit($review->review, 120) }}</p>
                                    @endif
                                    <div class="profile-review-card-actions">
                                        <a href="{{ route('books.show', $review->book->slug) }}" class="profile-review-card-link">
                                            <i class="fas fa-book-open"></i> View Book
                                        </a>
                                        <button class="profile-review-card-link" onclick="editReview({{ $review->id }})">
                                            <i class="fas fa-pen"></i> Edit
                                        </button>
                                        <button class="profile-review-card-link profile-review-card-link-danger" onclick="deleteReview({{ $review->id }})">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($reviews->hasPages())
                        <div class="profile-pagination">
                            {{ $reviews->links('vendor.pagination.default') }}
                        </div>
                    @endif
                @else
                    <div class="profile-empty">
                        <i class="fas fa-star-half-stroke"></i>
                        <p>No reviews yet. Reviews you write will appear here.</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ================================================================ --}}
{{-- CHANGE EMAIL MODAL --}}
{{-- ================================================================ --}}
<div class="profile-modal-overlay" id="emailModalOverlay" onclick="closeEmailModal()">
    <div class="profile-modal" onclick="event.stopPropagation()">
        <button class="profile-modal-close" onclick="closeEmailModal()"><i class="fas fa-xmark"></i></button>
        <h3 class="profile-modal-title">Change Email Address</h3>
        <p class="profile-modal-subtitle">Enter your current password and new email.</p>
        <form action="{{ route('customer.profile.email') }}" method="POST" class="profile-modal-form">
            @csrf
            <div class="profile-form-group">
                <label class="profile-label">Current Password</label>
                <input type="password" name="current_password" class="profile-input" required>
            </div>
            <div class="profile-form-group">
                <label class="profile-label">New Email</label>
                <input type="email" name="email" class="profile-input" required placeholder="newemail@example.com">
            </div>
            <div class="profile-modal-actions">
                <button type="button" class="profile-btn profile-btn-outline" onclick="closeEmailModal()">Cancel</button>
                <button type="submit" class="profile-btn profile-btn-primary">Update Email</button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================================ --}}
{{-- ADDRESS MODAL --}}
{{-- ================================================================ --}}
<div class="profile-modal-overlay" id="addressModalOverlay" onclick="closeAddressModal()">
    <div class="profile-modal" onclick="event.stopPropagation()">
        <button class="profile-modal-close" onclick="closeAddressModal()"><i class="fas fa-xmark"></i></button>
        <h3 class="profile-modal-title" id="addressModalTitle">Add New Address</h3>
        <form action="{{ route('customer.address.store') }}" method="POST" class="profile-modal-form" id="addressModalForm">
            @csrf
            <input type="hidden" name="_method" id="addressModalMethod" value="POST">
            <div class="profile-form-group">
                <label class="profile-label">Receiver Name</label>
                <input type="text" name="receiver_name" id="addrModalName" class="profile-input" required placeholder="Full name">
            </div>
            <div class="profile-form-group">
                <label class="profile-label">Phone Number</label>
                <input type="tel" name="phone_number" id="addrModalPhone" class="profile-input" maxlength="11" required placeholder="09123456789"
                       oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)">
            </div>
            <div class="profile-form-group">
                <label class="profile-label">Address</label>
                <textarea name="address_line" id="addrModalLine" class="profile-input profile-textarea" rows="3" required placeholder="Street, City, Region"></textarea>
            </div>
            <div class="profile-modal-actions">
                <button type="button" class="profile-btn profile-btn-outline" onclick="closeAddressModal()">Cancel</button>
                <button type="submit" class="profile-btn profile-btn-primary" id="addressModalSubmitBtn">
                    <i class="fas fa-plus"></i> Add Address
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/customer/profile.js') }}"></script>
@endpush