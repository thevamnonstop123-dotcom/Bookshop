<div class="profile-card">
    <div class="profile-card-header"><h3 class="profile-card-title">My Addresses</h3><button class="profile-btn profile-btn-primary profile-btn-sm" onclick="openAddressModal()"><i class="fas fa-plus"></i> Add Address</button></div>
    @forelse($addresses as $address)
        <div class="profile-address-card {{ $address->is_default ? 'profile-address-card-default' : '' }}">
            <div class="profile-address-header"><div class="profile-address-receiver"><i class="fas fa-user"></i> {{ $address->receiver_name }}</div>@if($address->is_default)<span class="profile-address-badge">Default</span>@endif</div>
            <div class="profile-address-phone"><i class="fas fa-phone"></i> {{ $address->phone_number }}</div>
            <div class="profile-address-line"><i class="fas fa-location-dot"></i> {{ $address->address_line }}</div>
            <div class="profile-address-actions">
                <button class="profile-address-btn" onclick="editAddressModal({{ $address->id }}, '{{ $address->receiver_name }}', '{{ $address->phone_number }}', '{{ $address->address_line }}')"><i class="fas fa-pen-to-square"></i> Edit</button>
                @if(!$address->is_default)
                    <form action="{{ route('customer.address.default', $address->id) }}" method="POST" style="display:inline;">@csrf @method('PATCH')<button type="submit" class="profile-address-btn profile-address-btn-default"><i class="fas fa-star"></i> Set Default</button></form>
                @endif
                <form action="{{ route('customer.address.delete', $address->id) }}" method="POST" onsubmit="return confirm('Delete?')" style="display:inline;">@csrf @method('DELETE')<button type="submit" class="profile-address-btn profile-address-btn-delete"><i class="fas fa-trash"></i> Delete</button></form>
            </div>
        </div>
    @empty
        <div class="profile-empty"><i class="fas fa-map-pin"></i><p>No addresses saved yet.</p></div>
    @endforelse
</div>
