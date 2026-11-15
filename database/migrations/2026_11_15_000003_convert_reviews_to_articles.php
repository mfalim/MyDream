<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->foreignId('event_id')->nullable()->after('id')->constrained('events')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->after('event_id')->constrained('clients')->nullOnDelete();
            $table->unsignedTinyInteger('rating')->nullable()->after('client_id');
            $table->unique('event_id');
            $table->index('client_id');
        });

        if (Schema::hasTable('reviews')) {
            $reviews = DB::table('reviews')
                ->leftJoin('events', 'reviews.event_id', '=', 'events.id')
                ->leftJoin('bookings', 'events.booking_id', '=', 'bookings.id')
                ->select([
                    'reviews.id',
                    'reviews.event_id',
                    'reviews.client_id',
                    'reviews.rating',
                    'reviews.comment',
                    'reviews.created_at',
                    'reviews.updated_at',
                    'events.name as event_name',
                    'events.status as event_status',
                    'events.package_id as event_package_id',
                    'bookings.status as booking_status',
                    'bookings.package_id as booking_package_id',
                ])
                ->orderBy('reviews.id')
                ->get();

            foreach ($reviews as $review) {
                $title = trim((string) $review->event_name) ?: 'Cerita Pernikahan';
                $slugBase = Str::slug($title) ?: 'cerita-pernikahan';
                $slug = $slugBase;
                $suffix = 2;

                while (DB::table('articles')->where('slug', $slug)->exists()) {
                    $slug = $slugBase . '-' . $suffix++;
                }

                $comment = trim((string) $review->comment);
                $content = 'Rating acara: ' . (int) $review->rating . '/5 bintang.';
                if ($comment !== '') {
                    $content .= "\n\n" . $comment;
                }

                $isPublishable = $review->event_status === 'completed' && $review->booking_status === 'approved';
                $createdAt = $review->created_at ?? now();

                DB::table('articles')->insert([
                    'title' => $title,
                    'slug' => $slug,
                    'category' => 'Ulasan Client',
                    'excerpt' => $comment !== '' ? Str::limit($comment, 300) : null,
                    'content' => $content,
                    'photo' => null,
                    'author' => 'Client MyDream',
                    'published_at' => $isPublishable ? substr((string) $createdAt, 0, 10) : null,
                    'package_id' => $review->event_package_id ?: $review->booking_package_id,
                    'is_published' => $isPublishable,
                    'event_id' => $review->event_id,
                    'client_id' => $review->client_id,
                    'rating' => (int) $review->rating,
                    'created_at' => $createdAt,
                    'updated_at' => $review->updated_at ?? $createdAt,
                ]);
            }

            Schema::drop('reviews');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->unsignedTinyInteger('rating');
                $table->text('comment')->nullable();
                $table->timestamps();
                $table->unique('event_id');
            });
        }

        if (Schema::hasColumn('articles', 'event_id')) {
            $reviewArticles = DB::table('articles')->whereNotNull('event_id')->get();
            foreach ($reviewArticles as $article) {
                if (! $article->client_id || ! $article->rating) {
                    continue;
                }

                DB::table('reviews')->updateOrInsert(
                    ['event_id' => $article->event_id],
                    [
                        'client_id' => $article->client_id,
                        'rating' => $article->rating,
                        'comment' => $article->content,
                        'created_at' => $article->created_at,
                        'updated_at' => $article->updated_at,
                    ],
                );
            }

            DB::table('articles')->whereNotNull('event_id')->delete();

            Schema::table('articles', function (Blueprint $table): void {
                $table->dropForeign(['event_id']);
                $table->dropForeign(['client_id']);
                $table->dropUnique(['event_id']);
                $table->dropIndex(['client_id']);
                $table->dropColumn(['event_id', 'client_id', 'rating']);
            });
        }
    }
};
