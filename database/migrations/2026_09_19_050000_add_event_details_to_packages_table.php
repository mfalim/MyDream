<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('event_period')->nullable()->after('name');
            $table->unsignedInteger('duration')->nullable()->after('event_period');
            $table->unsignedInteger('guest_capacity')->nullable()->after('duration');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['event_period', 'duration', 'guest_capacity']);
        });
    }
};
