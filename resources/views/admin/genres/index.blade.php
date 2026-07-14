@extends('layouts.admin')

@section('title', 'Genres — Bookshop Admin')
@section('page_title', 'Genre Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
@endpush

@section('content')
<div class="admin-table-card">
    <div class="admin-table-header">
        <div class="admin-table-header-left">
            <h2 class="admin-table-title">All Genres</h2>
            <span class="admin-table-count">{{ $genres->count() }} genres</span>
        </div>
        <button class="admin-btn admin-btn-primary" onclick="document.getElementById('addGenreForm').style.display='block'; document.getElementById('genreName').focus();">
            <i class="fas fa-plus"></i> Add Genre
        </button>
    </div>

    {{-- Quick Add Form --}}
    <div id="addGenreForm" style="display:none; padding: var(--space-2) var(--space-3); border-bottom: 1px solid var(--color-border-light);">
        <form action="{{ route('admin.genres.store') }}" method="POST" style="display:flex; gap: var(--space-2);">
            @csrf
            <input type="text" name="name" id="genreName" class="admin-form-input" placeholder="Genre name" required style="flex:1;">
            <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm">Save</button>
            <button type="button" class="admin-btn admin-btn-ghost admin-btn-sm" onclick="document.getElementById('addGenreForm').style.display='none'">Cancel</button>
        </form>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th>Name</th>
                    <th style="width:100px;">Authors</th>
                    <th style="width:160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($genres as $genre)
                    <tr>
                        <td class="admin-table-index">{{ $loop->iteration }}</td>
                        <td>
                            <span class="admin-table-name">{{ $genre->name }}</span>
                        </td>
                        <td class="admin-table-number">{{ $genre->authors_count }}</td>
                        <td>
                            <div class="admin-table-actions">
                                <button class="admin-action-btn admin-action-edit" onclick="editGenre({{ $genre->id }}, '{{ $genre->name }}')" title="Edit">
                                    <i class="fas fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('admin.genres.destroy', $genre) }}" method="POST" onsubmit="return confirm('Delete this genre?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="admin-action-btn admin-action-delete" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="admin-table-empty">
                                <div class="admin-table-empty-icon"><i class="fas fa-tags"></i></div>
                                <h4>No genres found</h4>
                                <p>Add genres to categorize authors.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editGenreModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.3); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:var(--color-surface); border-radius:var(--radius-xl); padding:var(--space-4); width:90%; max-width:400px;">
        <h3 style="margin-bottom:var(--space-2);">Edit Genre</h3>
        <form id="editGenreForm" method="POST">
            @csrf @method('PUT')
            <input type="text" name="name" id="editGenreName" class="admin-form-input" required style="margin-bottom:var(--space-2);">
            <div style="display:flex; gap:var(--space-2); justify-content:flex-end;">
                <button type="button" class="admin-btn admin-btn-ghost admin-btn-sm" onclick="document.getElementById('editGenreModal').style.display='none'">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function editGenre(id, name) {
    document.getElementById('editGenreModal').style.display = 'flex';
    document.getElementById('editGenreForm').action = '/admin/genres/' + id;
    document.getElementById('editGenreName').value = name;
    document.getElementById('editGenreName').focus();
}
document.getElementById('editGenreModal').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>

@endsection