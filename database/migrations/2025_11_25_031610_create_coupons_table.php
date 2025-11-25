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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Mã coupon (VD: SALE50)
            $table->string('description')->nullable();

            // Loại giảm giá: phần trăm (percent) hoặc tiền mặt (fixed)
            $table->enum('type', ['percent', 'fixed'])->default('fixed');
            $table->decimal('value', 12, 2); // Giá trị giảm (VD: 10% hoặc 50.000đ)

            // Điều kiện áp dụng
            $table->decimal('min_order_amount', 12, 2)->default(0); // Đơn tối thiểu
            $table->integer('usage_limit')->nullable(); // Giới hạn số lần dùng
            $table->integer('used_count')->default(0); // Đã dùng bao nhiêu lần

            // Thời gian hiệu lực
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'coupon_code')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('coupon_code')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
