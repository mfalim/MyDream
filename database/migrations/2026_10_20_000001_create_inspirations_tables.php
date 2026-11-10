<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cerita pernikahan yang diisi client SETELAH acaranya berlalu, ditinjau admin sebelum tayang di /inspirasi.
        if (! Schema::hasTable('inspirations')) {
            Schema::create('inspirations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('booking_id')->nullable()->unique()->constrained('bookings')->nullOnDelete(); // 1 cerita per pesanan
                $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('theme');
                // Data berikut disalin dari pesanan saat dikirim, bukan diketik client (agar tidak palsu).
                $table->string('couple_names');
                $table->string('venue_name')->nullable();
                $table->string('city')->nullable();
                $table->date('event_date')->nullable();
                $table->unsignedInteger('guest_count')->nullable();
                $table->boolean('show_couple_names')->default(true);
                // Isi yang ditulis client.
                $table->longText('story');
                $table->text('tips')->nullable();
                $table->text('testimonial')->nullable();
                $table->unsignedTinyInteger('rating')->nullable();
                // Alur tinjauan admin: pending | approved | rejected
                $table->string('status')->default('pending');
                $table->text('admin_note')->nullable();
                $table->timestamp('consented_at')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('inspiration_photos')) {
            Schema::create('inspiration_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inspiration_id')->constrained('inspirations')->cascadeOnDelete();
                $table->string('photo');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inspiration_photos');
        Schema::dropIfExists('inspirations');
    }
};
