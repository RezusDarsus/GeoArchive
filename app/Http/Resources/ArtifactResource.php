<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArtifactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'period' => $this->period,
            'location' => $this->location,
            'category' => $this->whenLoaded('category', fn () => $this->category?->name),
            'tags' => $this->whenLoaded('tags', fn () => $this->tags->pluck('name')),
            'image_url' => $this->image ? asset('storage/'.$this->image) : null,
            'connected_events' => $this->whenLoaded('historicalEvents', fn () => $this->historicalEvents->pluck('title')),
            'links' => [
                'web' => route('artifacts.show', $this->id),
                'api' => url('/api/artifacts/'.$this->id),
            ],
        ];
    }
}
