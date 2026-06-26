<?php

namespace App\Providers;

use App\Models\Artifact;
use App\Models\Category;
use App\Models\HistoricalEvent;
use App\Models\Tag;
use App\Policies\ArtifactPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\HistoricalEventPolicy;
use App\Policies\TagPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Artifact::class, ArtifactPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(HistoricalEvent::class, HistoricalEventPolicy::class);
        Gate::policy(Tag::class, TagPolicy::class);

        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));
    }
}
