@extends('layouts.admin')

@section('title', 'Review Management — Bookshop Admin')
@section('page_title', 'Reviews')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/payment.css') }}">
@endpush

@section('content')

{{-- Stats --}}
<div class="payment-stats-grid">
    <div class="payment-stat-card payment-stat-revenue">
        <div class="payment-stat-icon"><i class="fas fa-star"></i></div>
        <div class="payment-stat-info">
            <span class="payment-stat-label">Total Reviews</span>
            <span class="payment-stat-value">{{ number_format($stats['total']) }}</span>
        </div>
    </div>
    <div class="payment-stat-card payment-stat-completed">
        <div class="payment-stat-icon"><i class="fas fa-chart-line"></i></div>
        <div class="payment-stat-info">
            <span class="payment-stat-label">Average Rating</span>
            <span class="payment-stat-value">{{ $stats['average'] }} / 5</span>
        </div>
    </div>
    <div class="payment-stat-card payment-stat-pending">
        <div class="payment-stat-icon"><i class="fas fa-eye-slash"></i></div>
        <div class="payment-stat-info">
            <span class="payment-stat-label">Hidden</span>
            <span class="payment-stat-value">{{ $stats['hidden'] }}</span>
        </div>
    </div>
    <div class="payment-stat-card payment-stat-failed">
        <div class="payment-stat-icon"><i class="fas fa-flag"></i></div>
        <div class="payment-stat-info">
            <span class="payment-stat-label">Reported</span>
            <span class="payment-stat-value">{{ $stats['reported'] }}</span>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="order-filter-tabs">
    <a href="{{ route('admin.reviews.index') }}" class="order-filter-tab {{ empty($filters['status']) ? 'order-filter-tab-active' : '' }}">
        <i class="fas fa-list"></i> All
    </a>
    <a href="{{ route('admin.reviews.index', ['status' => 'active']) }}" class="order-filter-tab {{ ($filters['status'] ?? '') === 'active' ? 'order-filter-tab-active' : '' }}">
        <i class="fas fa-check-circle"></i> Active
    </a>
    <a href="{{ route('admin.reviews.index', ['status' => 'hidden']) }}" class="order-filter-tab {{ ($filters['status'] ?? '') === 'hidden' ? 'order-filter-tab-active' : '' }}">
        <i class="fas fa-eye-slash"></i> Hidden
    </a>
    <a href="{{ route('admin.reviews.index', ['status' => 'reported']) }}" class="order-filter-tab {{ ($filters['status'] ?? '') === 'reported' ? 'order-filter-tab-active' : '' }}">
        <i class="fas fa-flag"></i> Reported
    </a>
</div>

{{-- Table --}}
<div class="admin-table-card">
    <div class="admin-table-header">
        <div class="admin-table-header-left">
            <h2 class="admin-table-title">All Reviews</h2>
            <span class="admin-table-count">{{ $reviews->total() }} reviews</span>
        </div>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Book</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Helpful</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                    <tr>
                        <td>
                            <div class="admin-table-name">{{ $review->customer->name ?? 'Unknown' }}</div>
                            <div class="admin-table-meta">{{ $review->customer->email ?? '' }}</div>
                        </td>
                        <td>
                            <div class="admin-table-name">{{ Str::limit($review->book->title ?? 'N/A', 30) }}</div>
                        </td>
                        <td>
                            <span style="color:var(--color-accent);">
                                @for($i=1;$i<=5;$i++)
                                    <i class="fas fa-star{{ $i <= $review->rating ? '' : '-empty' }}" style="font-size:11px;"></i>
                                @endfor
                            </span>
                        </td>
                        <td>
                            <div class="admin-table-bio">{{ Str::limit($review->review, 60) ?: '—' }}</div>
                        </td>
                        <td>
                            <span class="admin-table-number">{{ $review->helpful_count }}</span>
                        </td>
                        <td>
                            <span class="admin-badge admin-badge-{{ $review->status === 'active' ? 'success' : ($review->status === 'hidden' ? 'warning' : 'danger') }}">
                                {{ ucfirst($review->status) }}
                            </span>
                        </td>
                        <td class="admin-table-date">{{ $review->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="admin-table-actions">
                                @if($review->status === 'active')
                                    <form action="{{ route('admin.reviews.status', $review) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="hidden">
                                        <button class="admin-action-btn admin-action-edit" title="Hide"><i class="fas fa-eye-slash"></i></button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.reviews.status', $review) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="active">
                                        <button class="admin-action-btn admin-action-view" title="Restore"><i class="fas fa-check-circle"></i></button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Delete permanently?')">
                                    @csrf @method('DELETE')
                                    <button class="admin-action-btn admin-action-delete" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="admin-table-empty">
                                <div class="admin-table-empty-icon"><i class="fas fa-star"></i></div>
                                <h4>No reviews found</h4>
                                <p>Customer reviews will appear here.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reviews->hasPages())
        <div class="admin-table-pagination">
            {{ $reviews->appends(request()->query())->links('vendor.pagination.default') }}
        </div>
    @endif
</div>

@endsection