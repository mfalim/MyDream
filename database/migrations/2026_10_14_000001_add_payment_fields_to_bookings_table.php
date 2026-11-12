<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'invoice_number')) {
                $table->string('invoice_number')->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('bookings', 'paid_amount')) {
                $table->decimal('paid_amount', 15, 2)->default(0)->after('total_price');
            }

            // unpaid | awaiting_verification | dp_paid | paid  (terpisah dari kolom `status` = persetujuan admin)
            if (! Schema::hasColumn('bookings', 'payment_status')) {
                $table->string('payment_status')->default('unpaid')->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            foreach (['payment_status', 'paid_amount', 'invoice_number'] as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
