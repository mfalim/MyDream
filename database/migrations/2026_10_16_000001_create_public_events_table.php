<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Event PUBLIK MyDream (pameran, open house, workshop) yang diumumkan admin.
        // Terpisah dari tabel `events` yang berisi acara pernikahan milik client.
        if (! Schema::hasTable('public_events')) {
            Schema::create('public_events', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('photo')->nullable();
                $table->date('starts_on');
                $table->date('ends_on')->nullable();        // kosong = satu hari
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->string('location')->nullable();
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        // Vendor yang berpartisipasi di sebuah event.
        if (! Schema::hasTable('public_event_vendor')) {
            Schema::create('public_event_vendor', function (Blueprint $table) {
                $table->id();
                $table->foreignId('public_event_id')->constrained('public_events')->cascadeOnDelete();
                $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
                $table->unique(['public_event_id', 'vendor_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('public_event_vendor');
        Schema::dropIfExists('public_events');
    }
};
