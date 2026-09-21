<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE packages MODIFY duration DECIMAL(8, 2) NULL');

        Schema::table('packages', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('guest_capacity');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        DB::statement('ALTER TABLE packages MODIFY duration INT UNSIGNED NULL');
    }
};
