<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_vendors', function (Blueprint $table) {
            $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
            $table->foreign('vendor_id')->references('id')->on('vendors')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_vendors', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropForeign(['vendor_id']);
        });
    }
};
