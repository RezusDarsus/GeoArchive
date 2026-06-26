<?php

namespace Tests\Feature;

use App\Models\Artifact;
use App\Models\Category;
use App\Models\HistoricalEvent;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_public_pages_render_archive_content(): void
    {
        $this->seed();

        $this->get('/')->assertOk()->assertSee('GeoArchive')->assertSee('Alaverdi Cathedral');
        $this->get('/artifacts')->assertOk()->assertSee('Vani Bronze Figurine');
        $coin = Artifact::where('title', 'Vani Colchis Coin')->firstOrFail();
        $this->get(route('artifacts.show', $coin))->assertOk()->assertSee('Colchis');
        $this->get('/events')->assertOk()->assertSee('Battle of Didgori');
        $colchis = HistoricalEvent::where('title', 'Colchis in Western Georgia')->firstOrFail();
        $this->get(route('events.show', $colchis))
            ->assertOk()
            ->assertSee('Colchis in Western Georgia')
            ->assertSee('advanced bronze and iron working')
            ->assertSee('storage/events/colchis-western-georgia.png', false);
        $didgori = HistoricalEvent::where('title', 'Battle of Didgori')->firstOrFail();
        $this->get(route('events.show', $didgori))->assertOk()->assertSee('12 August 1121');
        $this->get('/categories')
            ->assertOk()
            ->assertSee('Manuscript')
            ->assertSee('oldest continuous literary traditions')
            ->assertSee('storage/categories/manuscript.jpg', false);
        $category = Category::where('name', 'Archaeological Object')->firstOrFail();
        $this->get(route('categories.show', $category))
            ->assertOk()->assertSee('Archaeological Object')->assertSee('Vani Bronze Figurine');
        $tag = Tag::where('name', 'Colchis')->firstOrFail();
        $this->get(route('tags.show', $tag))
            ->assertOk()->assertSee('Colchis')->assertSee('Vani Colchis Coin');
        $this->get(route('history-paths.index'))
            ->assertOk()
            ->assertSee('History paths through Georgia')
            ->assertSee('From Bronze Age Georgia to Colchis')
            ->assertSee(route('events.show', $didgori), false)
            ->assertSee('The modern struggle for independence');
    }

    public function test_demo_seeder_is_repeatable_without_duplicate_records(): void
    {
        $this->seed();
        $this->seed();

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('categories', 7);
        $this->assertDatabaseCount('tags', 12);
        $this->assertDatabaseCount('historical_events', 37);
        $this->assertDatabaseCount('artifacts', 22);
        $this->assertDatabaseCount('artifact_historical_event', 19);
    }

    public function test_connected_history_is_bidirectional_and_visible(): void
    {
        $this->seed();

        $artifact = Artifact::where('title', 'Didgori-period Battle Axe')->firstOrFail();
        $event = HistoricalEvent::where('title', 'Battle of Didgori')->firstOrFail();

        $this->assertTrue($artifact->historicalEvents->contains($event));
        $this->assertTrue($event->artifacts->contains($artifact));
        $this->get(route('artifacts.show', $artifact))->assertOk()->assertSee('Battle of Didgori');
        $this->get(route('events.show', $event))->assertOk()->assertSee('Didgori-period Battle Axe');
    }

    public function test_every_seeded_archive_record_has_a_local_image(): void
    {
        $this->seed();

        $paths = Category::pluck('image')
            ->merge(Artifact::pluck('image'))
            ->merge(HistoricalEvent::pluck('image'));

        $this->assertCount(66, $paths);
        $paths->each(function (?string $path): void {
            $this->assertNotNull($path);
            $this->assertTrue(Storage::disk('public')->exists($path), "Missing seeded image: {$path}");
        });
    }

    public function test_every_seeded_archive_image_is_unique(): void
    {
        $this->seed();

        $paths = Category::pluck('image')
            ->merge(Artifact::pluck('image'))
            ->merge(HistoricalEvent::pluck('image'));
        $hashes = $paths->map(fn (string $path): string => hash_file('sha256', Storage::disk('public')->path($path)));

        $this->assertCount(66, $hashes);
        $this->assertCount(66, $hashes->unique(), 'Two or more seeded records use the same image file.');
    }

    public function test_every_seeded_image_has_a_distinct_source_record(): void
    {
        $sources = json_decode(file_get_contents(base_path('IMAGE_SOURCES.json')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertCount(66, $sources);
        $this->assertCount(66, collect($sources)->pluck('Record')->unique());
        $this->assertCount(66, collect($sources)->pluck('WikimediaFile')->unique());
    }

    public function test_every_artifact_and_event_has_at_least_700_words_of_structured_history(): void
    {
        $this->seed();

        $records = Artifact::get(['title', 'description'])
            ->concat(HistoricalEvent::get(['title', 'description']));

        $this->assertCount(59, $records);
        $records->each(function ($record): void {
            preg_match_all("/[\\p{L}\\p{N}’'-]+/u", $record->description, $matches);
            $wordCount = count($matches[0]);

            $this->assertGreaterThanOrEqual(700, $wordCount, "{$record->title} has only {$wordCount} words.");
            $this->assertStringContainsString('HISTORICAL SETTING', $record->description);
            $this->assertStringContainsString('QUESTIONS FOR FURTHER STUDY', $record->description);
        });
    }
}
