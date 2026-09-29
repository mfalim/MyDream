<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('packages', 'duration')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropColumn('duration');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('packages', 'duration')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->decimal('duration', 8, 2)->nullable()->after('availability_date');
            });
        }
    }
};
