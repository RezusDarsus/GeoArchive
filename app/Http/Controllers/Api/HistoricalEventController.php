<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HistoricalEventResource;
use App\Models\HistoricalEvent;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HistoricalEventController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return HistoricalEventResource::collection(
            HistoricalEvent::chronological()->paginate(12)
        );
    }

    public function show(HistoricalEvent $event): HistoricalEventResource
    {
        return new HistoricalEventResource($event->load('artifacts'));
    }
}
