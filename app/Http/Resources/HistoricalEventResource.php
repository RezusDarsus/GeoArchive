<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoricalEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'date_or_period' => $this->date_or_period,
            'sort_year' => $this->sort_year,
            'location' => $this->location,
            'connected_artifacts' => $this->whenLoaded('artifacts', fn () => $this->artifacts->pluck('title')),
            'links' => [
                'web' => route('events.show', $this->id),
                'api' => url('/api/events/'.$this->id),
            ],
        ];
    }
}
