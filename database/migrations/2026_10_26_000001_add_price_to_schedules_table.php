<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->nullable()->after('vendor_id');
        });

        DB::table('schedules')
            ->join('vendors', 'schedules.vendor_id', '=', 'vendors.id')
            ->whereNotNull('schedules.vendor_id')
            ->update(['schedules.price' => DB::raw('vendors.price')]);
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
