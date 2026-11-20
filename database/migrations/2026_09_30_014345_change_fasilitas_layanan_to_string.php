```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('layanan', function (Blueprint $table) {
            $table->string('grand_entrance_gate')->nullable()->change();
            $table->string('meja_akad')->nullable()->change();
            $table->string('aisle_carpet')->nullable()->change();
            $table->string('photo_booth')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('layanan', function (Blueprint $table) {
            $table->integer('grand_entrance_gate')->nullable()->change();
            $table->integer('meja_akad')->nullable()->change();
            $table->integer('aisle_carpet')->nullable()->change();
            $table->integer('photo_booth')->nullable()->change();
        });
    }
};
