@extends('layouts.admin')

@section('title', 'Promotions — Bookshop Admin')
@section('page_title', 'Email Promotions')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
@endpush

@section('content')
    {{-- Send Promotion Form --}}
    <div class="admin-form-card" style="max-width: 100%; margin-bottom: 28px;">
        <div class="admin-form-card-header">
            <div class="admin-form-card-icon" style="background: #FFFBEB; color: #F59E0B;">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div>
                <h2 class="admin-form-card-title">Send Promotion</h2>
                <p class="admin-form-card-subtitle">
                    Send an email to all <strong>{{ $activeCustomersCount ?? \App\Models\Customer::where('status', 'active')->count() }}</strong> active customers
                </p>
            </div>
        </div>

        <form action="{{ route('admin.promotions.send') }}" method="POST" class="admin-form">
            @csrf

            <div class="admin-form-grid">
                <div class="admin-form-group admin-form-group-full">
                    <label for="promoSubject" class="admin-form-label">
                        Subject <span class="admin-form-required">*</span>
                    </label>
                    <div class="admin-form-input-wrapper">
                        <i class="fas fa-heading admin-form-input-icon"></i>
                        <input type="text" id="promoSubject" name="subject"
                               class="admin-form-input" placeholder="e.g., 40% Off — New Arrivals!" required>
                    </div>
                </div>

                <div class="admin-form-group admin-form-group-full">
                    <label for="promoMessage" class="admin-form-label">
                        Message <span class="admin-form-required">*</span>
                    </label>
                    <textarea id="promoMessage" name="message"
                              class="admin-form-input admin-form-textarea"
                              rows="6" placeholder="Write your promotion message here..." required></textarea>
                    <div class="admin-form-description-actions" style="margin-top:8px;">
                        <button type="button" class="admin-btn admin-btn-ghost admin-btn-sm" onclick="generatePromotionMessage()" id="generatePromoBtn">
                            <i class="fas fa-wand-magic-sparkles"></i> Generate with AI
                        </button>
                        <span class="admin-form-loading" id="aiPromoLoading" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i> Generating...
                        </span>
                    </div>
            </div>

            <div class="admin-form-actions">
                <button type="submit" class="admin-btn admin-btn-primary">
                    <i class="fas fa-paper-plane"></i>
                    Send to {{ $activeCustomersCount ?? \App\Models\Customer::where('status', 'active')->count() }} Customers
                </button>
            </div>
        </form>
    </div>

    {{-- Sent Promotions Table --}}
    <div class="admin-table-card">
        <div class="admin-table-header">
            <div class="admin-table-header-left">
                <h2 class="admin-table-title">Sent Promotions</h2>
                <span class="admin-table-count">{{ $promotions->count() }} {{ Str::plural('campaign', $promotions->count()) }}</span>
            </div>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th style="width: 100px;">Recipients</th>
                        <th style="width: 130px;">Sent By</th>
                        <th style="width: 140px;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($promotions as $promo)
                        <tr>
                            <td class="admin-table-index">{{ $loop->iteration }}</td>
                            <td>
                                <div class="admin-table-name">{{ $promo->subject }}</div>
                            </td>
                            <td class="admin-table-bio">{{ Str::limit($promo->message, 80) }}</td>
                            <td class="admin-table-number">
                                <span class="admin-badge admin-badge-info">
                                    <i class="fas fa-users"></i> {{ $promo->recipients_count }}
                                </span>
                            </td>
                            <td>
                                <div class="admin-table-name" style="font-size: 12px;">{{ $promo->sentBy->name ?? 'System' }}</div>
                            </td>
                            <td class="admin-table-date">{{ $promo->sent_at->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="admin-table-empty">
                                    <div class="admin-table-empty-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <h4>No promotions sent yet</h4>
                                    <p>Create your first email campaign to engage with your customers.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

@push('scripts')
<script>
function generatePromotionMessage() {
    const subject = document.getElementById('promoSubject').value;
    if (!subject || subject.length < 5) {
        alert('Please enter a subject first (at least 5 characters).');
        return;
    }
    const btn = document.getElementById('generatePromoBtn');
    const loading = document.getElementById('aiPromoLoading');
    btn.disabled = true;
    btn.style.display = 'none';
    loading.style.display = 'inline-block';
    fetch('/admin/ai/generate-promotion', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ subject: subject, audience: 'all' })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('promoMessage').value = data.message || '';
    })
    .catch(err => alert('Failed to generate. Try again.'))
    .finally(() => {
        btn.disabled = false;
        btn.style.display = '';
        loading.style.display = 'none';
    });
}
</script>
@endpush

@endsection