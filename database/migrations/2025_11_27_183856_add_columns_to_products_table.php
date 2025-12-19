<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('market_price', 15, 2)->nullable()->after('price')->comment('Giá niêm yết trước khi giảm');
            $table->string('warranty')->nullable()->after('quantity')->comment('Thời gian bảo hành của sản phẩm');
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
