<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->after('username');
            $table->timestamp('email_verified_at')->nullable()->after('email');
        });

        DB::table('users')->where('role', 'owner')->update(['role' => 'super_admin']);
        DB::table('users')->where('role', 'staff')->update(['role' => 'user']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email', 'email_verified_at']);
        });

        DB::table('users')->where('role', 'super_admin')->update(['role' => 'owner']);
        DB::table('users')->where('role', 'user')->update(['role' => 'staff']);
    }
};
