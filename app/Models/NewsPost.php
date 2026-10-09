<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class NewsPost extends Model
{
    public const LOCALES = ['fr', 'en', 'de'];

    protected $fillable = [
        'slug', 'image', 'image_alt',
        'title_fr', 'excerpt_fr', 'content_fr',
        'title_en', 'excerpt_en', 'content_en',
        'title_de', 'excerpt_de', 'content_de',
        'is_published', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where('published_at', '<=', now());
    }

    /** Value for a locale, falling back to French when that translation is empty. */
    public function localized(string $field, string $locale): ?string
    {
        return $this->{"{$field}_{$locale}"} ?: $this->{"{$field}_fr"};
    }

    public function imageUrl(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }
}
