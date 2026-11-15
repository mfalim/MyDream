<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('bookings', 'invoice_number')) {
            return;
        }

        DB::table('bookings')
            ->whereNull('invoice_number')
            ->orderBy('id')
            ->chunkById(200, function ($bookings): void {
                foreach ($bookings as $booking) {
                    $date = $booking->created_at
                        ? Carbon::parse($booking->created_at)->format('Ymd')
                        : now()->format('Ymd');
                    $invoiceBase = sprintf('MD-%s-LEGACY-%06d', $date, $booking->id);
                    $invoiceNumber = $invoiceBase;
                    $suffix = 1;

                    while (DB::table('bookings')->where('invoice_number', $invoiceNumber)->exists()) {
                        $invoiceNumber = $invoiceBase . '-' . $suffix++;
                    }

                    DB::table('bookings')
                        ->where('id', $booking->id)
                        ->whereNull('invoice_number')
                        ->update(['invoice_number' => $invoiceNumber]);
                }
            });
    }

    public function down(): void
    {
        // Invoice identifiers are retained after backfill because they may already have been shared.
    }
};
