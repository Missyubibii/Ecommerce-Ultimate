<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f7f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .header { background: #f43f5e; padding: 40px 20px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; }
        .content { padding: 40px; color: #374151; line-height: 1.6; }
        .content h2 { color: #111827; font-size: 20px; margin-top: 0; }
        .btn { display: inline-block; padding: 14px 32px; background-color: #f43f5e; color: #ffffff !important; text-decoration: none; border-radius: 12px; font-weight: 700; margin: 30px 0; }
        .footer { padding: 20px 40px; background: #f9fafb; color: #6b7280; font-size: 13px; text-align: center; border-top: 1px solid #e5e7eb; }
        .warning { background: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; font-size: 14px; color: #92400e; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="content">
            <h2>Yêu cầu đặt lại mật khẩu</h2>
            <p>Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>
            
            <div style="text-align: center;">
                <a href="{{ $url }}" class="btn">Đặt lại mật khẩu</a>
            </div>
            
            <div class="warning">
                Liên kết đặt lại mật khẩu này sẽ hết hạn sau <strong>{{ $count }} phút</strong>.
            </div>
            
            <p>Nếu bạn không yêu cầu đặt lại mật khẩu, bạn không cần thực hiện thêm hành động nào.</p>
            
            <div style="height: 1px; background: #e5e7eb; margin: 30px 0;"></div>
            
            <p style="font-size: 12px; color: #9ca3af; word-break: break-all;">Nếu bạn gặp sự cố khi nhấp vào nút "Đặt lại mật khẩu", hãy sao chép và dán URL bên dưới vào trình duyệt web của bạn: <br>{{ $url }}</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
