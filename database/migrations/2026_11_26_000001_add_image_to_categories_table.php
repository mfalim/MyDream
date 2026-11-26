<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('categories', 'image')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('image')->nullable()->after('name');
            });
        }

        // Isi gambar lokal (tanpa CDN) hanya untuk kategori yang sudah ada.
        $defaultImages = [
            'Venue & Ballroom' => 'images/categories/venue-ballroom.jpg',
            'Dekorasi & Lighting' => 'images/categories/dekorasi-lighting.jpg',
            'Fotografi & Videografi' => 'images/categories/fotografi-videografi.jpg',
            'Katering & Kue' => 'images/categories/katering-kue.jpg',
            'Makeup & Hair' => 'images/categories/makeup-hair.jpg',
            'Gaun & Busana Pengantin' => 'images/categories/gaun-busana.jpg',
            'Entertainment & Sound' => 'images/categories/entertainment-sound.jpg',
        ];

        foreach ($defaultImages as $name => $path) {
            DB::table('categories')
                ->where('name', $name)
                ->whereNull('image')
                ->update(['image' => $path]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('categories', 'image')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
};
