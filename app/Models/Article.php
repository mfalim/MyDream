<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'excerpt',
        'content',
        'photo',
        'author',
        'published_at',
        'package_id',
        'is_published',
    ];

    protected $casts = [
        'published_at' => 'date',
        'is_published' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Slug dibuat otomatis dari judul bila kosong, dan dijamin unik.
        static::saving(function (Article $article) {
            if (blank($article->slug)) {
                $base = Str::slug($article->title) ?: 'artikel';
                $slug = $base;
                $i = 2;

                while (static::where('slug', $slug)->where('id', '!=', $article->id ?? 0)->exists()) {
                    $slug = $base . '-' . $i++;
                }

                $article->slug = $slug;
            }
        });
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /** Tayang: dipublikasikan dan tanggal tayangnya belum di masa depan. */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', today()));
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('published_at')->orderByDesc('id');
    }

    public function getDateLabelAttribute(): string
    {
        return ($this->published_at ?? $this->created_at)->copy()->locale('id')->translatedFormat('d F Y');
    }

    /** Ringkasan: excerpt jika ada, kalau tidak potongan awal isi artikel. */
    public function getSummaryAttribute(): string
    {
        return $this->excerpt ?: Str::limit(preg_replace('/\s+/', ' ', strip_tags((string) $this->content)), 160);
    }
}
