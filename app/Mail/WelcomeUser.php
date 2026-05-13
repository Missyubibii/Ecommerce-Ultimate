<?php
// app/Mail/WelcomeUser.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeUser extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $coupon;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $coupon = null)
    {
        $this->user = $user;
        $this->coupon = $coupon;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Chào mừng bạn đến với ' . config('app.name') . '!')
                    ->view('emails.welcome');
    }
}
