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
        Schema::table('products', function (Blueprint $table) {
        // Thêm giá niêm yết (giá gạch ngang)
        $table->decimal('market_price', 15, 2)->nullable()->after('price')->comment('Giá niêm yết trước khi giảm');

        // Thêm thông tin bảo hành
        $table->string('warranty')->nullable()->after('quantity')->comment('Ví dụ: 12 tháng, 2 năm');

        // Thêm ưu đãi đặc biệt
        $table->text('special_offer')->nullable()->after('description')->comment('Quà tặng hoặc ưu đãi kèm theo');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
        $table->dropColumn(['market_price', 'warranty', 'special_offer']);
    });
    }
};
