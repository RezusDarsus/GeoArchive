<?php

use App\Http\Controllers\Api\ArtifactController;
use App\Http\Controllers\Api\HistoricalEventController;
use Illuminate\Support\Facades\Route;

/*
| Read-only JSON API for the public archive. These endpoints return Laravel
| API Resources (JSON) and are automatically prefixed with /api and given the
| "api" middleware group (stateless, rate-limited). They demonstrate basic API
| endpoints and JSON responses alongside the server-rendered Blade interface.
*/

Route::get('/artifacts', [ArtifactController::class, 'index']);
Route::get('/artifacts/{artifact}', [ArtifactController::class, 'show']);
Route::get('/events', [HistoricalEventController::class, 'index']);
Route::get('/events/{event}', [HistoricalEventController::class, 'show']);
