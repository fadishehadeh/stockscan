<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 180);
            $table->string('phone', 60)->nullable();
            $table->string('company', 140)->nullable();
            $table->string('business_type', 120)->nullable();
            $table->text('message');
            $table->string('recipient_email', 180)->nullable();
            $table->boolean('email_delivered')->default(false);
            $table->text('delivery_error')->nullable();
            $table->timestamp('submitted_at');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['email_delivered', 'submitted_at']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_inquiries');
    }
};
