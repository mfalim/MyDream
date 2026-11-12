<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('api_carts') && ! Schema::hasTable('carts')) {
            Schema::rename('api_carts', 'carts');
        }

        if (Schema::hasTable('api_cart_vendors') && ! Schema::hasTable('cart_vendor')) {
            Schema::rename('api_cart_vendors', 'cart_vendor');

            if (Schema::hasColumn('cart_vendor', 'api_cart_id')) {
                Schema::table('cart_vendor', function (Blueprint $table) {
                    $table->renameColumn('api_cart_id', 'cart_id');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cart_vendor') && ! Schema::hasTable('api_cart_vendors')) {
            if (Schema::hasColumn('cart_vendor', 'cart_id')) {
                Schema::table('cart_vendor', function (Blueprint $table) {
                    $table->renameColumn('cart_id', 'api_cart_id');
                });
            }

            Schema::rename('cart_vendor', 'api_cart_vendors');
        }

        if (Schema::hasTable('carts') && ! Schema::hasTable('api_carts')) {
            Schema::rename('carts', 'api_carts');
        }
    }
};
