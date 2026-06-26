<?php

namespace Tests\Unit;

use App\Services\PublicImageStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class PublicImageStorageTest extends TestCase
{
    public function test_failed_operation_removes_newly_stored_file(): void
    {
        Storage::fake('public');
        $service = app(PublicImageStorage::class);
        $image = UploadedFile::fake()->createWithContent(
            'test.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wl2nR0AAAAASUVORK5CYII='),
        );

        try {
            $service->storeSafely($image, 'artifacts', fn () => throw new RuntimeException('failure'));
            $this->fail('Expected storage operation to fail.');
        } catch (RuntimeException $exception) {
            $this->assertSame('failure', $exception->getMessage());
        }

        $this->assertSame([], Storage::disk('public')->allFiles());
    }
}
