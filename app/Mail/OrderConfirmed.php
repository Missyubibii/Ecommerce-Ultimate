<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Đơn hàng #' . $this->order->order_number . ' đã được xác nhận')
                    ->view('emails.orders.status')
                    ->with([
                        'status_text' => 'Đã xác nhận',
                        'status_color' => '#10b981', // Green
                        'message_body' => 'Đơn hàng của bạn đã được xác nhận và đang trong quá trình chuẩn bị hàng.'
                    ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
