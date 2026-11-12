<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('type')->nullable();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('cart_vendor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->unique(['cart_id', 'vendor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_vendor');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('api_cart_vendors');
        Schema::dropIfExists('api_carts');
    }
};
