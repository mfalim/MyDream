<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            if (! Schema::hasColumn('schedules', 'agreed_fee')) {
                $table->decimal('agreed_fee', 15, 2)->nullable()->after('status');
            }
            if (! Schema::hasColumn('schedules', 'paid_amount')) {
                $table->decimal('paid_amount', 15, 2)->default(0)->after('agreed_fee');
            }
            if (! Schema::hasColumn('schedules', 'payment_status')) {
                // unpaid | dp_paid | paid
                $table->string('payment_status', 30)->default('unpaid')->after('paid_amount');
            }
            if (! Schema::hasColumn('schedules', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_status');
            }
            if (! Schema::hasColumn('schedules', 'payment_notes')) {
                $table->text('payment_notes')->nullable()->after('paid_at');
            }
        });

        // Backfill agreed_fee dari harga master vendor untuk jadwal vendor yang sudah ada
        $schedules = DB::table('schedules')
            ->whereNotNull('vendor_id')
            ->whereNull('agreed_fee')
            ->get(['id', 'vendor_id']);

        foreach ($schedules as $schedule) {
            $vendorPrice = DB::table('vendors')->where('id', $schedule->vendor_id)->value('price');
            if ($vendorPrice !== null) {
                DB::table('schedules')
                    ->where('id', $schedule->id)
                    ->update(['agreed_fee' => $vendorPrice]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            foreach (['payment_notes', 'paid_at', 'payment_status', 'paid_amount', 'agreed_fee'] as $column) {
                if (Schema::hasColumn('schedules', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

