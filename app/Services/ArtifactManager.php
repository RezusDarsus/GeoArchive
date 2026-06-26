<?php

namespace App\Services;

use App\Models\Artifact;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ArtifactManager
{
    public function __construct(private readonly PublicImageStorage $images) {}

    public function create(User $owner, array $data, ?UploadedFile $image): Artifact
    {
        $tags = $data['tags'] ?? [];
        $events = $data['events'] ?? [];
        unset($data['tags'], $data['events'], $data['image'], $data['remove_image']);

        return $this->images->storeSafely($image, 'artifacts', function (?string $path) use ($owner, $data, $tags, $events): Artifact {
            if ($path) {
                $data['image'] = $path;
            }

            return DB::transaction(function () use ($owner, $data, $tags, $events): Artifact {
                $artifact = $owner->artifacts()->create($data);
                $artifact->tags()->sync($tags);
                $artifact->historicalEvents()->sync($events);

                return $artifact;
            });
        });
    }

    public function update(Artifact $artifact, array $data, ?UploadedFile $image, bool $removeImage): Artifact
    {
        $tags = $data['tags'] ?? [];
        $events = $data['events'] ?? [];
        unset($data['tags'], $data['events'], $data['image'], $data['remove_image']);
        $oldImage = $artifact->image;

        $newImage = $this->images->storeSafely($image, 'artifacts', function (?string $path) use ($artifact, $data, $tags, $events, $removeImage): ?string {
            if ($path) {
                $data['image'] = $path;
            } elseif ($removeImage) {
                $data['image'] = null;
            }
            DB::transaction(function () use ($artifact, $data, $tags, $events): void {
                $artifact->update($data);
                $artifact->tags()->sync($tags);
                $artifact->historicalEvents()->sync($events);
            });

            return $path;
        });

        if ($newImage || $removeImage) {
            $this->images->delete($oldImage);
        }

        return $artifact->refresh();
    }

    public function delete(Artifact $artifact): void
    {
        $image = $artifact->image;
        DB::transaction(fn () => $artifact->delete());
        $this->images->delete($image);
    }
}
