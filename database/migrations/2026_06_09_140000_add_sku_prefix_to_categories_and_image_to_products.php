<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('sku_prefix', 12)->nullable()->unique()->after('name');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('description');
        });

        $usedPrefixes = [];
        $categories = DB::table('categories')->select('id', 'name')->orderBy('id')->get();

        foreach ($categories as $category) {
            $base = Str::upper(Str::substr(preg_replace('/[^A-Za-z0-9]/', '', $category->name) ?: 'CAT', 0, 4));
            $prefix = $base;
            $counter = 2;

            while (in_array($prefix, $usedPrefixes, true)) {
                $suffix = (string) $counter;
                $prefix = Str::substr($base, 0, max(1, 4 - strlen($suffix))) . $suffix;
                $counter++;
            }

            $usedPrefixes[] = $prefix;

            DB::table('categories')->where('id', $category->id)->update([
                'sku_prefix' => $prefix,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['sku_prefix']);
            $table->dropColumn('sku_prefix');
        });
    }
};
