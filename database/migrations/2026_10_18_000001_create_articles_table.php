<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('articles')) {
            return;
        }

        // Artikel & cerita pernikahan yang ditulis admin, tampil di halaman /blog.
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('Artikel');
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('photo')->nullable();
            $table->string('author')->nullable();
            $table->date('published_at')->nullable();      // tanggal tayang; kosong = langsung tayang
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
