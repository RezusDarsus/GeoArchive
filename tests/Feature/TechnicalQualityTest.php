<?php

namespace Tests\Feature;

use App\Models\Artifact;
use App\Models\Category;
use App\Models\HistoricalEvent;
use App\Models\Tag;
use App\Models\User;
use App\Services\PublicImageStorage;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TechnicalQualityTest extends TestCase
{
    use RefreshDatabase;

    private function png(string $name = 'image.png', int $width = 1, int $height = 1): UploadedFile
    {
        $chunk = fn (string $type, string $data): string => pack('N', strlen($data)).$type.$data.pack('N', crc32($type.$data));
        $rows = str_repeat("\0".str_repeat("\0\0\0", $width), $height);
        $png = "\x89PNG\r\n\x1a\n"
            .$chunk('IHDR', pack('NNCCCCC', $width, $height, 8, 2, 0, 0, 0))
            .$chunk('IDAT', gzcompress($rows))
            .$chunk('IEND', '');

        return UploadedFile::fake()->createWithContent($name, $png);
    }

    public function test_archive_policies_allow_only_administrators(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::create(['name' => 'Architecture']);
        $artifact = Artifact::create([
            'title' => 'Policy Monument',
            'description' => 'A monument used to verify policy authorization.',
            'category_id' => $category->id,
            'user_id' => $admin->id,
        ]);

        $this->assertTrue(Gate::forUser($admin)->allows('update', $artifact));
        $this->assertTrue(Gate::forUser($admin)->allows('create', HistoricalEvent::class));
        $this->assertFalse(Gate::forUser($user)->allows('update', $artifact));
        $this->assertFalse(Gate::forUser($user)->allows('create', HistoricalEvent::class));

        foreach ([Category::class, Tag::class, HistoricalEvent::class] as $model) {
            $this->assertTrue(Gate::forUser($admin)->allows('viewAny', $model));
            $this->assertTrue(Gate::forUser($admin)->allows('create', $model));
            $this->assertFalse(Gate::forUser($user)->allows('viewAny', $model));
            $this->assertFalse(Gate::forUser($user)->allows('create', $model));
        }
    }

    public function test_form_requests_reject_duplicate_titles_and_invalid_uploads(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Church']);
        Artifact::create([
            'title' => 'Unique Cathedral',
            'description' => 'An existing historical cathedral record.',
            'category_id' => $category->id,
            'user_id' => $admin->id,
        ]);

        $this->actingAs($admin)->post(route('admin.artifacts.store'), [
            'title' => 'Unique Cathedral',
            'description' => 'A duplicate title that must be rejected.',
            'category_id' => $category->id,
            'image' => UploadedFile::fake()->create('unsafe.txt', 10, 'text/plain'),
        ])->assertSessionHasErrors(['title', 'image']);

        $this->assertDatabaseCount('artifacts', 1);
        $this->assertCount(0, Storage::disk('public')->allFiles());
    }

    public function test_public_archive_search_filter_and_sort_are_functional(): void
    {
        $this->seed();

        $tag = Tag::where('name', 'Colchis')->firstOrFail();
        $category = Category::where('name', 'Archaeological Object')->firstOrFail();

        $this->get(route('artifacts.index', ['q' => 'Armazi']))
            ->assertOk()->assertSee('Armazi Bilingual Stele')->assertDontSee('Khevsurian Sword');
        $this->get(route('artifacts.index', ['category' => $category->id]))
            ->assertOk()->assertSee('Vani Bronze Figurine')->assertDontSee('Alaverdi Cathedral');
        $this->get(route('artifacts.index', ['tag' => $tag->id]))
            ->assertOk()->assertSee('Vani Colchis Coin')->assertDontSee('Alaverdi Cathedral');
        $this->get(route('events.index', ['q' => 'Krtsanisi']))
            ->assertOk()->assertSee('Battle of Krtsanisi')->assertDontSee('Battle of Didgori');
        $this->get(route('events.index', ['sort' => 'newest']))
            ->assertOk()->assertSeeInOrder(['Russo-Georgian War', 'Rose Revolution']);
        $this->get(route('artifacts.index', ['q' => 'Georgia']))
            ->assertOk()->assertSee('q=Georgia', false);
    }

    public function test_image_replacement_and_explicit_removal_delete_old_files(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Architecture']);
        $oldImage = $this->png('old.png')->store('artifacts', 'public');
        $artifact = Artifact::create([
            'title' => 'Storage Monument',
            'description' => 'A monument used to test safe image replacement.',
            'category_id' => $category->id,
            'user_id' => $admin->id,
            'image' => $oldImage,
        ]);

        $this->actingAs($admin)->put(route('admin.artifacts.update', $artifact), [
            'title' => $artifact->title,
            'description' => $artifact->description,
            'category_id' => $category->id,
            'image' => $this->png('new.png'),
        ])->assertRedirect();

        $artifact->refresh();
        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertExists($artifact->image);
        $replacement = $artifact->image;

        $this->put(route('admin.artifacts.update', $artifact), [
            'title' => $artifact->title,
            'description' => $artifact->description,
            'category_id' => $category->id,
            'remove_image' => 1,
        ])->assertRedirect();

        $this->assertNull($artifact->fresh()->image);
        Storage::disk('public')->assertMissing($replacement);
    }

    public function test_transaction_safe_storage_removes_a_new_file_after_failure(): void
    {
        Storage::fake('public');
        $storage = app(PublicImageStorage::class);

        try {
            $storage->storeSafely($this->png('temporary.png'), 'artifacts', function (): void {
                throw new \RuntimeException('Simulated database failure');
            });
            $this->fail('The simulated failure was not thrown.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Simulated database failure', $exception->getMessage());
        }

        $this->assertCount(0, Storage::disk('public')->allFiles());
    }

    public function test_database_constraints_and_image_dimensions_are_enforced(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Church']);
        Artifact::create([
            'title' => 'Database Unique Record',
            'description' => 'First database integrity record.',
            'category_id' => $category->id,
            'user_id' => $admin->id,
        ]);

        $this->actingAs($admin)->post(route('admin.artifacts.store'), [
            'title' => 'Oversized Dimensions',
            'description' => 'This upload exceeds the allowed pixel dimensions.',
            'category_id' => $category->id,
            'image' => $this->png('wide.png', 6001, 1),
        ])->assertSessionHasErrors('image');

        $this->expectException(QueryException::class);
        Artifact::create([
            'title' => 'Database Unique Record',
            'description' => 'Second database integrity record.',
            'category_id' => $category->id,
            'user_id' => $admin->id,
        ]);
    }
}
