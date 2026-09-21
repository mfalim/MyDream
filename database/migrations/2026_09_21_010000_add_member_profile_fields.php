<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('call_sign')->nullable()->after('name');
            $table->string('tier')->nullable()->after('position');
            $table->string('emergency_name')->nullable()->after('ht_code');
            $table->string('emergency_phone')->nullable()->after('emergency_name');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'call_sign',
                'tier',
                'emergency_name',
                'emergency_phone',
            ]);
        });
    }
};
