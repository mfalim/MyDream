<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateUserId = DB::table('vendors')
            ->select('user_id')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->value('user_id');

        if ($duplicateUserId !== null) {
            throw new RuntimeException(
                'Each vendor account must be linked to one vendor record. Resolve duplicate vendors.user_id values before migrating.'
            );
        }

        Schema::table('vendors', function (Blueprint $table) {
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
        });
    }
};
