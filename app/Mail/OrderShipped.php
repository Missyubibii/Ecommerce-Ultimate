<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderShipped extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Đơn hàng #' . $this->order->order_number . ' đang được giao')
                    ->view('emails.orders.status')
                    ->with([
                        'status_text' => 'Đang giao hàng',
                        'status_color' => '#f59e0b', // Amber
                        'message_body' => 'Đơn hàng của bạn đã được bàn giao cho đơn vị vận chuyển và đang trên đường đến với bạn.'
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
