<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            // Basic app settings
            $table->string('app_name')->nullable()->after('id');
            $table->string('app_url')->nullable()->after('app_name');

            // Mail provider choice
            $table->string('mail_provider')->default('mailjet')->after('app_url');

            // Mailjet settings (encrypted)
            $table->string('mailjet_api_key')->nullable()->after('mail_provider');
            $table->string('mailjet_api_secret')->nullable()->after('mailjet_api_key');
            $table->string('mailjet_from_email')->nullable()->after('mailjet_api_secret');
            $table->string('mailjet_from_name')->nullable()->after('mailjet_from_email');

            // SMTP fallback settings
            $table->string('smtp_host')->nullable()->after('mailjet_from_name');
            $table->integer('smtp_port')->nullable()->after('smtp_host');
            $table->string('smtp_username')->nullable()->after('smtp_port');
            $table->string('smtp_password')->nullable()->after('smtp_username');

            // Email notification settings
            $table->boolean('notifications_enabled')->default(true)->after('smtp_password');
            $table->string('test_email_address')->nullable()->after('notifications_enabled');

            // Environment settings
            $table->string('app_env')->default('production')->after('test_email_address');
            $table->boolean('app_debug')->default(false)->after('app_env');
            $table->integer('session_lifetime')->default(60)->after('app_debug');
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn([
                'app_name', 'app_url',
                'mail_provider',
                'mailjet_api_key', 'mailjet_api_secret', 'mailjet_from_email', 'mailjet_from_name',
                'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password',
                'notifications_enabled', 'test_email_address',
                'app_env', 'app_debug', 'session_lifetime'
            ]);
        });
    }
};
