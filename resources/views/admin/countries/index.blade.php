@extends('layouts.admin')

@section('title', 'Countries — Bookshop Admin')
@section('page_title', 'Country Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
@endpush

@section('content')

@if(session('success'))
    <div class="admin-alert admin-alert-success">
        <i class="fas fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

<div class="admin-table-card">
    <div class="admin-table-header">
        <div class="admin-table-header-left">
            <h2 class="admin-table-title">All Countries</h2>
            <span class="admin-table-count">{{ $countries->count() }} countries</span>
        </div>
        <button class="admin-btn admin-btn-primary" onclick="document.getElementById('addCountryForm').style.display='block'; document.getElementById('countryName').focus();">
            <i class="fas fa-plus"></i> Add Country
        </button>
    </div>

    {{-- Quick Add Form --}}
    <div id="addCountryForm" style="display:none; padding: var(--space-2) var(--space-3); border-bottom: 1px solid var(--color-border-light);">
        <form action="{{ route('admin.countries.store') }}" method="POST" style="display:flex; gap: var(--space-2); align-items:flex-end;">
            @csrf
            <div style="flex:1;">
                <label class="admin-form-label">Country Name</label>
                <input type="text" name="name" id="countryName" class="admin-form-input" placeholder="e.g. Singapore" required>
            </div>
            <div style="width:100px;">
                <label class="admin-form-label">Code</label>
                <input type="text" name="code" class="admin-form-input" placeholder="SG" maxlength="2">
            </div>
            <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm">Save</button>
            <button type="button" class="admin-btn admin-btn-ghost admin-btn-sm" onclick="document.getElementById('addCountryForm').style.display='none'">Cancel</button>
        </form>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th>Name</th>
                    <th style="width:80px;">Code</th>
                    <th style="width:100px;">Authors</th>
                    <th style="width:160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($countries as $country)
                    <tr>
                        <td class="admin-table-index">{{ $loop->iteration }}</td>
                        <td>
                            <span class="admin-table-name">{{ $country->name }}</span>
                        </td>
                        <td>
                            <span class="admin-badge admin-badge-info">{{ strtoupper($country->code) }}</span>
                        </td>
                        <td class="admin-table-number">{{ $country->authors_count }}</td>
                        <td>
                            <div class="admin-table-actions">
                                <button class="admin-action-btn admin-action-edit" onclick="editCountry({{ $country->id }}, '{{ $country->name }}', '{{ $country->code }}')" title="Edit">
                                    <i class="fas fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('admin.countries.destroy', $country) }}" method="POST" onsubmit="return confirm('Delete this country?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="admin-action-btn admin-action-delete" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="admin-table-empty">
                                <div class="admin-table-empty-icon"><i class="fas fa-globe-asia"></i></div>
                                <h4>No countries found</h4>
                                <p>Add countries to assign to authors.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editCountryModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.3); z-index:9999; align-items:center; justify-content:center;" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:var(--color-surface); border-radius:var(--radius-xl); padding:var(--space-4); width:90%; max-width:400px;">
        <h3 style="margin-bottom:var(--space-2);">Edit Country</h3>
        <form id="editCountryForm" method="POST">
            @csrf @method('PUT')
            <div style="margin-bottom:var(--space-2);">
                <label class="admin-form-label">Country Name</label>
                <input type="text" name="name" id="editCountryName" class="admin-form-input" required>
            </div>
            <div style="margin-bottom:var(--space-2);">
                <label class="admin-form-label">Code</label>
                <input type="text" name="code" id="editCountryCode" class="admin-form-input" placeholder="SG" maxlength="2">
            </div>
            <div style="display:flex; gap:var(--space-2); justify-content:flex-end;">
                <button type="button" class="admin-btn admin-btn-ghost admin-btn-sm" onclick="document.getElementById('editCountryModal').style.display='none'">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function editCountry(id, name, code) {
    document.getElementById('editCountryModal').style.display = 'flex';
    document.getElementById('editCountryForm').action = '/admin/countries/' + id;
    document.getElementById('editCountryName').value = name;
    document.getElementById('editCountryCode').value = code;
    document.getElementById('editCountryName').focus();
}
</script>

@endsection