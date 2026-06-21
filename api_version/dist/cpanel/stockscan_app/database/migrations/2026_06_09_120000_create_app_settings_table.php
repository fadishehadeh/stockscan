<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('scanner_mode')->default('keyboard_wedge');
            $table->boolean('auto_submit_on_enter')->default(true);
            $table->string('default_post_scan_behavior')->default('open_product_actions');
            $table->string('default_stock_action')->default('out');
            $table->string('label_size_default')->default('medium');
            $table->string('barcode_prefix')->nullable();
            $table->unsignedTinyInteger('barcode_random_length')->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
