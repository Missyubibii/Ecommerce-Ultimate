<?php
// app/Mail/OrderPlaced.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Xác nhận đơn hàng #' . $this->order->order_number)
                    ->view('emails.orders.status')
                    ->with([
                        'status_text' => 'Đã đặt hàng',
                        'status_color' => '#6366f1', // Indigo
                        'message_body' => 'Cảm ơn bạn đã đặt hàng! Chúng tôi đã nhận được đơn hàng của bạn và đang tiến hành xử lý.'
                    ]);
    }
}
