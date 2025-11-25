<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, boolean, json, image
            $table->string('group')->default('general'); // general, payment, shipping
            $table->string('label')->nullable();
            $table->timestamps();
        });

        // Seed dữ liệu mẫu ngay khi migrate
        DB::table('settings')->insert([
            ['key' => 'site_name', 'value' => 'Laravel E-Commerce', 'type' => 'text', 'group' => 'general', 'label' => 'Tên Website'],
            ['key' => 'site_logo', 'value' => null, 'type' => 'image', 'group' => 'general', 'label' => 'Logo'],
            ['key' => 'contact_email', 'value' => 'admin@example.com', 'type' => 'text', 'group' => 'general', 'label' => 'Email Liên hệ'],
            ['key' => 'currency_symbol', 'value' => 'đ', 'type' => 'text', 'group' => 'general', 'label' => 'Đơn vị tiền tệ'],
            ['key' => 'freeship_threshold', 'value' => '500000', 'type' => 'number', 'group' => 'shipping', 'label' => 'Mức Freeship tối thiểu'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
