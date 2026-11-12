<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('midtrans_order_id')->nullable()->unique();
            $table->text('snap_token')->nullable();
            $table->string('transaction_status')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['midtrans_order_id']);
            $table->dropColumn(['midtrans_order_id', 'snap_token', 'transaction_status']);
        });
    }
};
