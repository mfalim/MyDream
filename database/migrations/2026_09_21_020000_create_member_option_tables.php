<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('member_specializations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        $now = now();

        DB::table('member_positions')->insert([
            ['name' => 'Lead Director', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Show Director', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Stage Manager', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Coordinator', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Crew', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Assistant', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('member_specializations')->insert([
            ['name' => 'Lighting', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Audio', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Hospitality', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('member_specializations');
        Schema::dropIfExists('member_positions');
    }
};
