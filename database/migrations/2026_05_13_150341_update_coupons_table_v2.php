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
        Schema::table('coupons', function (Blueprint $table) {
            // Renaming
            if (Schema::hasColumn('coupons', 'type')) {
                $table->renameColumn('type', 'discount_type');
            }
            if (Schema::hasColumn('coupons', 'value')) {
                $table->renameColumn('value', 'discount_value');
            }
            if (Schema::hasColumn('coupons', 'ends_at')) {
                $table->renameColumn('ends_at', 'expiry_date');
            }

            // Adding new columns
            $table->json('applicable_products')->nullable()->after('usage_limit');
            $table->json('applicable_categories')->nullable()->after('applicable_products');
            $table->json('applicable_brands')->nullable()->after('applicable_categories');
            $table->enum('channel', ['email', 'system', 'both'])->default('both')->after('applicable_brands');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->renameColumn('discount_type', 'type');
            $table->renameColumn('discount_value', 'value');
            $table->renameColumn('expiry_date', 'ends_at');

            $table->dropColumn(['applicable_products', 'applicable_categories', 'applicable_brands', 'channel']);
        });
    }
};
