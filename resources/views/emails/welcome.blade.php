<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chào mừng thành viên mới</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f7f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .header { background: #10b981; padding: 40px 20px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; }
        .content { padding: 40px; color: #374151; line-height: 1.6; }
        .content h2 { color: #111827; font-size: 20px; margin-top: 0; }
        .coupon-box { background: #ecfdf5; border: 2px dashed #10b981; border-radius: 12px; padding: 20px; margin: 30px 0; text-align: center; }
        .coupon-code { font-size: 28px; font-weight: 800; color: #065f46; letter-spacing: 2px; }
        .btn { display: inline-block; padding: 14px 32px; background-color: #10b981; color: #ffffff !important; text-decoration: none; border-radius: 12px; font-weight: 700; margin: 30px 0; }
        .footer { padding: 20px 40px; background: #f9fafb; color: #6b7280; font-size: 13px; text-align: center; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Chào mừng bạn gia nhập {{ config('app.name') }}!</h1>
        </div>
        <div class="content">
            <h2>Chào {{ $user->name }},</h2>
            <p>Tài khoản của bạn đã được xác nhận thành công. Cảm ơn bạn đã tin tưởng và lựa chọn mua sắm tại <strong>{{ config('app.name') }}</strong>.</p>
            
            @if($coupon)
                <p>Để chào mừng bạn, chúng tôi dành tặng bạn một mã giảm giá đặc biệt cho đơn hàng đầu tiên:</p>
                <div class="coupon-box">
                    <p style="margin: 0 0 10px 0; font-weight: 600; color: #065f46;">MÃ GIẢM GIÁ CỦA BẠN</p>
                    <div class="coupon-code">{{ $coupon->code }}</div>
                    <p style="margin: 10px 0 0 0; font-size: 14px;">
                        Giảm {{ $coupon->discount_type == 'percent' ? $coupon->discount_value . '%' : number_format($coupon->discount_value) . 'đ' }}
                        @if($coupon->min_order_amount > 0)
                            (Cho đơn từ {{ number_format($coupon->min_order_amount) }}đ)
                        @endif
                    </p>
                </div>
            @endif

            <p>Hãy bắt đầu khám phá những sản phẩm công nghệ mới nhất ngay bây giờ!</p>
            
            <div style="text-align: center;">
                <a href="{{ url('/') }}" class="btn">Mua sắm ngay</a>
            </div>
            
            <p>Nếu bạn cần hỗ trợ, đừng ngần ngại liên hệ với chúng tôi qua hotline hoặc chat trực tiếp trên website.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
