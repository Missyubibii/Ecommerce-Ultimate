<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin đơn hàng</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; }
        .container { max-width: 650px; margin: 20px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { background: #1e293b; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 22px; }
        .content { padding: 30px; color: #334155; }
        .status-badge { display: inline-block; padding: 6px 16px; border-radius: 20px; color: #ffffff; font-weight: bold; font-size: 14px; margin-bottom: 20px; }
        .order-info { background: #f8fafc; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
        .order-info table { width: 100%; border-collapse: collapse; }
        .order-info td { padding: 5px 0; font-size: 14px; }
        .order-info .label { color: #64748b; width: 120px; }
        .order-info .value { color: #1e293b; font-weight: 600; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { text-align: left; border-bottom: 2px solid #e2e8f0; padding: 10px 5px; color: #64748b; font-size: 13px; }
        .items-table td { padding: 15px 5px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .total-section { text-align: right; border-top: 2px solid #e2e8f0; padding-top: 20px; }
        .total-row { display: flex; justify-content: flex-end; gap: 20px; margin-bottom: 5px; }
        .total-label { color: #64748b; }
        .total-value { color: #1e293b; font-weight: bold; width: 120px; }
        .grand-total { font-size: 18px; color: #4f46e5 !important; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #4f46e5; color: #ffffff !important; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 20px; }
        .footer { padding: 20px; background: #f8fafc; text-align: center; color: #94a3b8; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="content">
            <div class="status-badge" style="background-color: {{ $status_color }};">
                {{ $status_text }}
            </div>
            
            <h2 style="margin-top: 0; color: #1e293b;">Chào {{ $order->user->name }},</h2>
            <p>{{ $message_body }}</p>
            
            <div class="order-info">
                <table>
                    <tr>
                        <td class="label">Mã đơn hàng:</td>
                        <td class="value">#{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">Ngày đặt:</td>
                        <td class="value">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Thanh toán:</td>
                        <td class="value">{{ strtoupper($order->payment_method) }}</td>
                    </tr>
                </table>
            </div>
            
            <h3 style="color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Chi tiết sản phẩm</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th style="text-align: center;">SL</th>
                        <th style="text-align: right;">Đơn giá</th>
                        <th style="text-align: right;">Tổng</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: #1e293b;">{{ $item->product->name }}</div>
                            @if($item->variation_details)
                                <div style="font-size: 12px; color: #64748b;">{{ $item->variation_details }}</div>
                            @endif
                        </td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">{{ number_format($item->price) }}đ</td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($item->price * $item->quantity) }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="total-section">
                <div style="float: right; width: 250px;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="text-align: left; color: #64748b;">Tạm tính:</td>
                            <td style="text-align: right; font-weight: 600;">{{ number_format($order->total_amount - $order->shipping_amount) }}đ</td>
                        </tr>
                        <tr>
                            <td style="text-align: left; color: #64748b;">Phí vận chuyển:</td>
                            <td style="text-align: right; font-weight: 600;">{{ number_format($order->shipping_amount) }}đ</td>
                        </tr>
                        <tr style="font-size: 18px; font-weight: bold; color: #4f46e5;">
                            <td style="text-align: left; padding-top: 10px;">Tổng cộng:</td>
                            <td style="text-align: right; padding-top: 10px;">{{ number_format($order->total_amount) }}đ</td>
                        </tr>
                    </table>
                </div>
                <div style="clear: both;"></div>
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <p style="font-size: 14px; color: #64748b;">Bạn có thể theo dõi chi tiết đơn hàng tại website của chúng tôi:</p>
                <a href="{{ route('customer.orders.show', $order->id) }}" class="btn">Xem chi tiết đơn hàng</a>
            </div>
        </div>
        
        <div class="footer">
            <p>Cảm ơn bạn đã tin tưởng lựa chọn {{ config('app.name') }}!</p>
            <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ hotline: 1900 6789 hoặc email: support@ultimate.vn</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
