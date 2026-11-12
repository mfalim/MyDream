<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('division')->nullable()->change();
            $table->string('position')->nullable()->change();
            $table->decimal('daily_fee', 15, 2)->nullable()->default(null)->change();
            $table->enum('status', ['standby', 'aktif', 'inaktif'])->default('standby')->change();
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->string('division')->nullable(false)->change();
            $table->string('position')->nullable(false)->change();
            $table->decimal('daily_fee', 15, 2)->nullable(false)->default(0)->change();
            $table->string('status')->nullable(false)->default('standby')->change();
        });
    }
};
