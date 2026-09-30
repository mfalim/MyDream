<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan', function (Blueprint $table) {
            $table->id();

            $table->string('nama_layanan');
            $table->string('kategori');
            $table->decimal('harga', 15, 2);

            $table->string('dimensi_panggung')->nullable();
            $table->string('daya_listrik_rigging')->nullable();
            $table->string('alokasi_kru')->nullable();
            $table->string('durasi_loading_teardown')->nullable();

            $table->text('deskripsi')->nullable();

            $table->boolean('grand_entrance_gate')->default(false);
            $table->boolean('meja_akad')->default(false);
            $table->boolean('aisle_carpet')->default(false);
            $table->boolean('photo_booth')->default(false);

            $table->string('foto_utama')->nullable();
            $table->string('gallery_1')->nullable();
            $table->string('gallery_2')->nullable();
            $table->string('gallery_3')->nullable();
            $table->string('gallery_4')->nullable();

            $table->enum('status', [
                'draft',
                'diajukan',
                'disetujui',
                'ditolak'
            ])->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan');
    }
};