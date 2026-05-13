<?php
// app/Listeners/SendWelcomeEmail.php

namespace App\Listeners;

use App\Mail\WelcomeUser;
use App\Models\Coupon;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmail
{
    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        $user = $event->user;

        try {
            // Tìm coupon khuyến mãi chào mừng
            $coupon = Coupon::where('is_active', true)
                ->whereIn('channel', ['email', 'both'])
                ->where('expiry_date', '>', now())
                ->orderBy('created_at', 'desc')
                ->first();

            // Gửi email chào mừng
            Mail::to($user->email)->send(new WelcomeUser($user, $coupon));

            Log::info("Welcome email sent to user: {$user->email}");
        } catch (\Exception $e) {
            Log::error("Failed to send welcome email to {$user->email}: " . $e->getMessage());
        }
    }
}
