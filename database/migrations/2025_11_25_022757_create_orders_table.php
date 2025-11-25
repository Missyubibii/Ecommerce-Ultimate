<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // VD: ORD20251126-XH512
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('status', [
                'pending',
                'paid',
                'processing',
                'shipped',
                'completed',
                'cancelled',
                'refunded'
            ])->default('pending')->index();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('shipping_amount', 12, 2)->default(0);
            $table->string('payment_method'); // cod, banking, etc.
            // Snapshot địa chỉ tại thời điểm đặt (tránh user sửa profile làm sai lệch đơn cũ)
            $table->json('shipping_address');
            $table->json('billing_address')->nullable();
            $table->json('metadata')->nullable(); // Chứa note, coupon code, v.v.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
