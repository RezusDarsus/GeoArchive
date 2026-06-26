<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PublicImageStorage
{
    public function store(?UploadedFile $image, string $directory): ?string
    {
        return $image?->store($directory, 'public');
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    public function storeSafely(?UploadedFile $image, string $directory, callable $operation): mixed
    {
        $path = $this->store($image, $directory);

        try {
            return $operation($path);
        } catch (Throwable $exception) {
            $this->delete($path);

            throw $exception;
        }
    }
}
