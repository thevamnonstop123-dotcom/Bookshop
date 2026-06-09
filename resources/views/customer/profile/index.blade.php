@extends('layouts.customer')

@section('title', 'My Profile - Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/profile.css') }}">
@endpush

@section('content')

<div class="profile-page">
    <div class="container">

        {{-- Header --}}
        <div class="profile-header">
            <img src="{{ $customer->image && $customer->image !== 'default.png' ? asset('storage/'.$customer->image) : 'https://ui-avatars.com/api/?name='.urlencode($customer->name).'&background=f59e0b&color=fff&size=72' }}"
                 alt="{{ $customer->name }}" class="profile-header-avatar">
            <div class="profile-header-info">
                <h1>{{ $customer->name }}</h1>
                <span>{{ $customer->email }} · Member since {{ $customer->created_at->format('M Y') }}</span>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success" style="margin-bottom:24px;">
                <i class="fas fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        <div class="profile-layout">

            {{-- Edit Profile --}}
            <div>
                <div class="profile-card">
                    <h3><i class="fas fa-user-edit"></i> Personal Information</h3>

                    <div class="profile-avatar-section">
                        <img src="{{ $customer->image && $customer->image !== 'default.png' ? asset('storage/'.$customer->image) : 'https://ui-avatars.com/api/?name='.urlencode($customer->name).'&background=f59e0b&color=fff&size=96' }}"
                             alt="{{ $customer->name }}" id="avatarPreview">
                    </div>

                    <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}"
                                   maxlength="11" required oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-control">
                                <option value="male" {{ $customer->gender=='male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ $customer->gender=='female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="{{ old('dob', $customer->dob->format('Y-m-d')) }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Profile Photo</label>
                            <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png" onchange="previewAvatar(event)">
                            <small style="color:var(--color-text-muted);font-size:11px;">Max 2MB. JPG, JPEG, PNG.</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-save">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </form>
                </div>
            </div>

            {{-- Addresses --}}
            <div>
                <div class="profile-card">
                    <h3><i class="fas fa-map-marker-alt"></i> My Addresses</h3>

                    @forelse($addresses as $address)
                        <div class="address-card {{ $address->is_default ? 'default' : '' }}">
                            @if($address->is_default)
                                <span class="address-default-badge">Default</span>
                            @endif
                            <div class="address-name">{{ $address->receiver_name }}</div>
                            <div class="address-phone">{{ $address->phone_number }}</div>
                            <div class="address-line">{{ $address->address_line }}</div>
                            <div class="address-actions">
                                <button class="btn btn-outline btn-sm"
                                        onclick="editAddress({{ $address->id }},'{{ $address->receiver_name }}','{{ $address->phone_number }}','{{ $address->address_line }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if(!$address->is_default)
                                    <form action="{{ route('customer.address.default', $address->id) }}" method="POST" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-outline btn-sm"><i class="fas fa-star"></i> Default</button>
                                    </form>
                                @endif
                                <form action="{{ route('customer.address.delete', $address->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this address?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p style="color:var(--color-text-muted);text-align:center;padding:20px;">No addresses saved yet.</p>
                    @endforelse

                    <div class="add-address-section">
                        <h4 id="addressFormTitle">Add New Address</h4>
                        <form action="{{ route('customer.address.store') }}" method="POST" id="addressForm">
                            @csrf
                            <input type="hidden" name="_method" id="addressMethod" value="POST">

                            <div class="form-group">
                                <label class="form-label">Receiver Name</label>
                                <input type="text" name="receiver_name" id="addrName" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone_number" id="addrPhone" class="form-control"
                                       maxlength="11" required oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Address</label>
                                <textarea name="address_line" id="addrLine" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-accent btn-sm" id="addressSubmitBtn">
                                <i class="fas fa-plus"></i> Add Address
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function previewAvatar(event) {
        document.getElementById('avatarPreview').src = URL.createObjectURL(event.target.files[0]);
    }
    function editAddress(id, name, phone, line) {
        document.getElementById('addressForm').action = '/profile/address/' + id;
        document.getElementById('addressMethod').value = 'PUT';
        document.getElementById('addrName').value = name;
        document.getElementById('addrPhone').value = phone;
        document.getElementById('addrLine').value = line;
        document.getElementById('addressFormTitle').textContent = 'Edit Address';
        document.getElementById('addressSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Update Address';
    }
</script>
@endpush