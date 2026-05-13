<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderDelivered extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Đơn hàng #' . $this->order->order_number . ' đã giao thành công')
                    ->view('emails.orders.status')
                    ->with([
                        'status_text' => 'Đã giao hàng',
                        'status_color' => '#059669', // Emerald
                        'message_body' => 'Đơn hàng của bạn đã được giao thành công. Cảm ơn bạn đã mua sắm tại ' . config('app.name') . '!'
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
