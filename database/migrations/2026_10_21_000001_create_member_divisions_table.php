<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        $now = now();

        DB::table('member_divisions')->insert([
            ['name' => 'show-management', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'client-hospitality', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'av-lighting', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'logistik-protokoler', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('member_divisions');
    }
};
