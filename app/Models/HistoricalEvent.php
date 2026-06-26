<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HistoricalEvent extends Model
{
    protected $fillable = ['title', 'description', 'date_or_period', 'sort_year', 'location', 'image'];

    protected function casts(): array
    {
        return ['sort_year' => 'integer'];
    }

    public function artifacts(): BelongsToMany
    {
        return $this->belongsToMany(Artifact::class)->orderBy('period');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        $like = '%'.trim($term).'%';

        return $query->where(fn (Builder $nested) => $nested->where('title', 'like', $like)
            ->orWhere('description', 'like', $like)
            ->orWhere('location', 'like', $like)
            ->orWhere('date_or_period', 'like', $like));
    }

    public function scopeChronological(Builder $query, bool $newestFirst = false): Builder
    {
        return $newestFirst
            ? $query->orderByDesc('sort_year')->orderByDesc('id')
            : $query->orderBy('sort_year')->orderBy('id');
    }
}
