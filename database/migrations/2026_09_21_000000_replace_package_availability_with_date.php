<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->date('availability_date')->nullable()->after('name');
            $table->dropColumn('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('guest_capacity');
            $table->dropColumn('availability_date');
        });
    }
};
