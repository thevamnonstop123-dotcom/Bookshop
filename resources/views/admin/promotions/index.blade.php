@extends('layouts.admin')

@section('title', 'Promotions - Bookshop Admin')
@section('page_title', 'Email Promotions')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
    <style>
        .promo-form { background: var(--color-white); border-radius: var(--radius-lg); padding: 28px; box-shadow: var(--shadow-sm); border: 1px solid var(--color-border-light); margin-bottom: 30px; }
        .promo-form h3 { margin-bottom: 18px; font-size: 17px; }
    </style>
@endpush

@section('content')

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom:20px;">
            <i class="fas fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Send Promotion Form --}}
    <div class="promo-form">
        <h3><i class="fas fa-bullhorn"></i> Send Promotion to All Customers</h3>
        <form action="{{ route('admin.promotions.send') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" placeholder="e.g., 40% Off — New Arrivals!" required>
            </div>
            <div class="form-group">
                <label class="form-label">Message</label>
                <textarea name="message" class="form-control" rows="6" placeholder="Write your promotion message here..." required></textarea>
            </div>
            <button type="submit" class="btn btn-accent">
                <i class="fas fa-paper-plane"></i> Send to {{ $activeCustomersCount ?? \App\Models\Customer::where('status','active')->count() }} Customers
            </button>
        </form>
    </div>

    {{-- History --}}
    <div class="table-container">
        <div class="table-header">
            <h2>Sent Promotions</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Recipients</th>
                    <th>Sent By</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($promotions as $promo)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="font-semibold">{{ $promo->subject }}</td>
                        <td>{{ Str::limit($promo->message, 60) }}</td>
                        <td>{{ $promo->recipients_count }}</td>
                        <td>{{ $promo->sentBy->name ?? 'N/A' }}</td>
                        <td>{{ $promo->sent_at->format('d M Y, H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding:40px;color:var(--color-text-muted);">
                            <i class="fas fa-envelope" style="font-size:40px;display:block;margin-bottom:10px;"></i>
                            No promotions sent yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
