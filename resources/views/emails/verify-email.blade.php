<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận Email</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f7f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .header { background: #4f46e5; padding: 40px 20px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.5px; }
        .content { padding: 40px; color: #374151; line-height: 1.6; }
        .content h2 { color: #111827; font-size: 20px; margin-top: 0; }
        .btn { display: inline-block; padding: 14px 32px; background-color: #4f46e5; color: #ffffff !important; text-decoration: none; border-radius: 12px; font-weight: 700; margin: 30px 0; transition: background 0.3s; }
        .btn:hover { background-color: #4338ca; }
        .footer { padding: 20px 40px; background: #f9fafb; color: #6b7280; font-size: 13px; text-align: center; border-top: 1px solid #e5e7eb; }
        .divider { height: 1px; background: #e5e7eb; margin: 30px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="content">
            <h2>Chào {{ $name }},</h2>
            <p>Chào mừng bạn đến với <strong>{{ config('app.name') }}</strong>! Chúng tôi rất vui khi có bạn là thành viên mới.</p>
            <p>Để kích hoạt tài khoản và bắt đầu trải nghiệm mua sắm tuyệt vời, vui lòng nhấn vào nút bên dưới để xác nhận địa chỉ email của bạn:</p>
            
            <div style="text-align: center;">
                <a href="{{ $url }}" class="btn">Xác nhận Email ngay</a>
            </div>
            
            <p>Nếu nút trên không hoạt động, bạn có thể sao chép và dán liên kết sau vào trình duyệt:</p>
            <p style="word-break: break-all; font-size: 12px; color: #9ca3af;">{{ $url }}</p>
            
            <div class="divider"></div>
            
            <p style="font-size: 14px;">Nếu bạn không tạo tài khoản này, vui lòng bỏ qua email này.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>Đây là email tự động, vui lòng không phản hồi.</p>
        </div>
    </div>
</body>
</html>
