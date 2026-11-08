<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('reviews')) {
            return;
        }

        if (Schema::hasColumn('reviews', 'vendor_id')) {
            $this->mergeVendorReviews();

            // The old composite unique index may also be supporting the event FK.
            // Drop existing foreign keys and indexes by their actual metadata so this
            // migration can resume safely after a partially completed MySQL ALTER.
            foreach (Schema::getForeignKeys('reviews') as $foreignKey) {
                if (in_array('vendor_id', $foreignKey['columns'], true)
                    || $foreignKey['columns'] === ['event_id']) {
                    Schema::table('reviews', fn (Blueprint $table) => $table->dropForeign($foreignKey['name']));
                }
            }

            foreach (Schema::getIndexes('reviews') as $index) {
                if (in_array('vendor_id', $index['columns'], true)) {
                    Schema::table('reviews', function (Blueprint $table) use ($index) {
                        $index['unique']
                            ? $table->dropUnique($index['name'])
                            : $table->dropIndex($index['name']);
                    });
                }
            }

            Schema::table('reviews', fn (Blueprint $table) => $table->dropColumn('vendor_id'));
        }

        $hasEventUnique = collect(Schema::getIndexes('reviews'))
            ->contains(fn ($index) => $index['unique'] && $index['columns'] === ['event_id']);

        if (! $hasEventUnique) {
            Schema::table('reviews', fn (Blueprint $table) => $table->unique('event_id'));
        }

        $hasEventForeign = collect(Schema::getForeignKeys('reviews'))
            ->contains(fn ($foreignKey) => $foreignKey['columns'] === ['event_id']);

        if (! $hasEventForeign) {
            Schema::table('reviews', fn (Blueprint $table) =>
                $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete()
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('reviews') || Schema::hasColumn('reviews', 'vendor_id')) {
            return;
        }

        foreach (Schema::getForeignKeys('reviews') as $foreignKey) {
            if ($foreignKey['columns'] === ['event_id']) {
                Schema::table('reviews', fn (Blueprint $table) => $table->dropForeign($foreignKey['name']));
            }
        }

        foreach (Schema::getIndexes('reviews') as $index) {
            if ($index['unique'] && $index['columns'] === ['event_id']) {
                Schema::table('reviews', fn (Blueprint $table) => $table->dropUnique($index['name']));
            }
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->after('event_id')->constrained('vendors')->cascadeOnDelete();
            $table->unique(['event_id', 'vendor_id']);
            $table->index('vendor_id');
            $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
        });
    }

    private function mergeVendorReviews(): void
    {
        $groups = DB::table('reviews')
            ->leftJoin('vendors', 'reviews.vendor_id', '=', 'vendors.id')
            ->select('reviews.*', 'vendors.name as vendor_name')
            ->orderBy('reviews.id')
            ->get()
            ->groupBy('event_id');

        foreach ($groups as $eventReviews) {
            if ($eventReviews->count() < 2) {
                continue;
            }

            $first = $eventReviews->first();
            $comments = $eventReviews
                ->filter(fn ($review) => filled($review->comment))
                ->map(fn ($review) => trim(($review->vendor_name ? $review->vendor_name . ': ' : '') . $review->comment))
                ->implode("\n\n");

            DB::table('reviews')->where('id', $first->id)->update([
                'rating' => (int) round($eventReviews->avg('rating')),
                'comment' => $comments !== '' ? $comments : null,
                'updated_at' => now(),
            ]);

            DB::table('reviews')->whereIn('id', $eventReviews->pluck('id')->skip(1))->delete();
        }
    }
};
