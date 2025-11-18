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
        // 1. Bảng Products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete(); // Link to Module B
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->decimal('price', 12, 2); // Giá bán
            $table->decimal('cost_price', 12, 2)->nullable(); // Giá nhập
            $table->unsignedInteger('quantity')->default(0); // Tồn kho
            $table->decimal('weight', 8, 3)->nullable(); // Cân nặng (kg)
            $table->enum('status', ['active', 'draft'])->default('draft');
            $table->string('image')->nullable(); // Ảnh đại diện chính (Thumbnail)
            $table->json('metadata')->nullable(); // Custom attributes
            $table->string('unit')->default('cái'); // Đơn vị tính
            $table->integer('min_stock')->default(0); // Cảnh báo tồn kho thấp
            $table->timestamps();
        });

        // 2. Bảng Product Images (Gallery - Ảnh phụ)
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('path');
            $table->string('alt')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
    }
};
