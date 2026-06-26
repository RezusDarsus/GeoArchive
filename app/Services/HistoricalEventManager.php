<?php

namespace App\Services;

use App\Models\HistoricalEvent;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class HistoricalEventManager
{
    public function __construct(private readonly PublicImageStorage $images) {}

    public function create(array $data, ?UploadedFile $image): HistoricalEvent
    {
        unset($data['image'], $data['remove_image']);

        return $this->images->storeSafely($image, 'events', function (?string $path) use ($data): HistoricalEvent {
            if ($path) {
                $data['image'] = $path;
            }

            return DB::transaction(fn () => HistoricalEvent::create($data));
        });
    }

    public function update(HistoricalEvent $event, array $data, ?UploadedFile $image, bool $removeImage): HistoricalEvent
    {
        unset($data['image'], $data['remove_image']);
        $oldImage = $event->image;
        $newImage = $this->images->storeSafely($image, 'events', function (?string $path) use ($event, $data, $removeImage): ?string {
            if ($path) {
                $data['image'] = $path;
            } elseif ($removeImage) {
                $data['image'] = null;
            }
            DB::transaction(fn () => $event->update($data));

            return $path;
        });
        if ($newImage || $removeImage) {
            $this->images->delete($oldImage);
        }

        return $event->refresh();
    }

    public function delete(HistoricalEvent $event): void
    {
        $image = $event->image;
        DB::transaction(fn () => $event->delete());
        $this->images->delete($image);
    }
}
