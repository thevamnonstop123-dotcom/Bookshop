<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        {!! file_get_contents(public_path('css/email/promotion.css')) !!}
    </style>
</head>
<body class="email-body">
    <div class="email-container">
        <div class="email-card">

            <div class="email-header">
                <a href="{{ url('/') }}" class="email-logo">
                    Book<span class="email-logo-accent">shop</span>
                </a>
                <div class="email-header-subtitle">Your Premium Online Bookstore</div>
                @if($badge)
                    <div class="email-badge">{{ $badge }}</div>
                @endif
            </div>

            <div class="email-body-content">
                <p class="email-greeting">Hello <strong>{{ $name }}</strong>,</p>

                <div class="email-message">
                    {!! nl2br(e($body)) !!}
                </div>

                @if($ctaLink)
                    <div class="email-cta">
                        <a href="{{ $ctaLink }}" class="email-cta-link">{{ $ctaText }} &rarr;</a>
                    </div>
                @endif

                <div class="email-divider"></div>

                <table class="email-quick-links" cellpadding="0" cellspacing="0">
                    <tr>
                        <td><a href="{{ url('/books') }}">Browse Books</a></td>
                        <td><a href="{{ url('/orders') }}">My Orders</a></td>
                        <td><a href="{{ url('/wishlist') }}">Wishlist</a></td>
                    </tr>
                </table>

                <p class="email-footer-text">
                    You received this email because you are subscribed to Bookshop.
                    @if($unsubscribeLink)
                        <a href="{{ $unsubscribeLink }}" style="color:#94A3B8;">Unsubscribe</a>
                    @endif
                </p>
            </div>

            <div class="email-footer">
                <p>&copy; {{ date('Y') }} Bookshop. All rights reserved.</p>
                <p>Educational Books &middot; Programming &middot; Novels &middot; Stationery</p>
            </div>
        </div>
    </div>
</body>
</html>
