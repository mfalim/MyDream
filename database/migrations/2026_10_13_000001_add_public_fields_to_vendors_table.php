<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            if (! Schema::hasColumn('vendors', 'city')) {
                $table->string('city')->nullable()->after('address');
            }

            if (! Schema::hasColumn('vendors', 'instagram')) {
                $table->string('instagram')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            foreach (['city', 'instagram'] as $column) {
                if (Schema::hasColumn('vendors', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
