<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('category_id')->constrained()->nullOnDelete();
            $table->integer('production_year')->nullable()->after('warranty');
            $table->string('origin')->nullable()->after('production_year');
            $table->string('condition')->nullable()->after('origin');
            $table->string('model_code')->nullable()->after('sku');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('brand_id');
            $table->dropColumn(['production_year', 'origin', 'condition', 'model_code']);
        });
    }
};
