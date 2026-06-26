<?php

namespace Tests\Feature;

use App\Models\Artifact;
use App\Models\HistoricalEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_artifact_api_returns_json_collection_and_single_item(): void
    {
        $this->seed();

        $this->getJson('/api/artifacts')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'title', 'category', 'tags', 'image_url', 'links' => ['web', 'api']]],
                'links',
                'meta',
            ]);

        $id = Artifact::value('id');

        $this->getJson('/api/artifacts/'.$id)
            ->assertOk()
            ->assertJsonPath('data.id', $id)
            ->assertJsonStructure(['data' => ['id', 'title', 'tags', 'connected_events']]);
    }

    public function test_event_api_returns_json(): void
    {
        $this->seed();

        $this->getJson('/api/events')
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'title', 'date_or_period', 'links' => ['web', 'api']]]]);

        $id = HistoricalEvent::value('id');

        $this->getJson('/api/events/'.$id)
            ->assertOk()
            ->assertJsonPath('data.id', $id);
    }

    public function test_missing_record_returns_json_404(): void
    {
        $this->getJson('/api/artifacts/999999')->assertNotFound();
    }

    public function test_artifact_api_supports_search_query(): void
    {
        $this->seed();

        $this->getJson('/api/artifacts?q=Khakhuli')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Khakhuli Triptych']);
    }
}
