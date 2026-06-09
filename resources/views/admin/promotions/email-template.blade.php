<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; padding: 30px; }
        .container { max-width: 560px; margin: 0 auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
        .header { background: linear-gradient(135deg, #0f172a, #1e293b); color: #fff; padding: 36px; text-align: center; }
        .header h1 { font-size: 24px; margin: 0; color: #f59e0b; }
        .body { padding: 36px; }
        .body p { color: #475569; font-size: 15px; line-height: 1.7; }
        .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; }
        .btn { display: inline-block; background: #f59e0b; color: #0f172a; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 15px; margin: 16px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bookshop</h1>
        </div>
        <div class="body">
            <p>Hello {{ $name }},</p>
            {!! nl2br(e($content)) !!}
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Bookshop. All rights reserved.<br>
            You received this email because you're a Bookshop customer.
        </div>
    </div>
</body>
</html>
