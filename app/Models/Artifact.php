<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Artifact extends Model
{
    protected $fillable = [
        'title', 'description', 'period', 'location', 'image', 'user_id', 'category_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function historicalEvents(): BelongsToMany
    {
        return $this->belongsToMany(HistoricalEvent::class)->orderBy('sort_year');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        $like = '%'.trim($term).'%';

        return $query->where(fn (Builder $nested) => $nested->where('title', 'like', $like)
            ->orWhere('description', 'like', $like)
            ->orWhere('location', 'like', $like));
    }

    public function scopeInCategory(Builder $query, ?int $categoryId): Builder
    {
        return $query->when($categoryId, fn (Builder $builder) => $builder->where('category_id', $categoryId));
    }

    public function scopeWithTag(Builder $query, ?int $tagId): Builder
    {
        return $query->when($tagId, fn (Builder $builder) => $builder->whereHas('tags', fn (Builder $tags) => $tags->whereKey($tagId)));
    }
}
