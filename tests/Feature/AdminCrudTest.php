<?php

namespace Tests\Feature;

use App\Models\Artifact;
use App\Models\Category;
use App\Models\HistoricalEvent;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_crud_categories_and_tags(): void
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->createWithContent('category.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wl2nR0AAAAASUVORK5CYII='));

        $this->actingAs($this->admin)->post('/admin/categories', ['name' => 'Coin', 'description' => 'Historic coins', 'image' => $image])->assertRedirect();
        $category = Category::firstOrFail();
        Storage::disk('public')->assertExists($category->image);
        $storedImage = $category->image;
        $this->put("/admin/categories/{$category->id}", ['name' => 'Coins', 'description' => 'Updated'])->assertRedirect();
        $this->delete("/admin/categories/{$category->id}")->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        Storage::disk('public')->assertMissing($storedImage);

        $this->post('/admin/tags', ['name' => 'Medieval'])->assertRedirect();
        $tag = Tag::firstOrFail();
        $this->put("/admin/tags/{$tag->id}", ['name' => 'Golden Age'])->assertRedirect();
        $this->delete("/admin/tags/{$tag->id}")->assertRedirect();
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    public function test_artifact_crud_relationships_validation_and_image_upload_work(): void
    {
        Storage::fake('public');
        $category = Category::create(['name' => 'Manuscript']);
        $tag = Tag::create(['name' => 'Medieval']);
        $image = UploadedFile::fake()->createWithContent('artifact.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wl2nR0AAAAASUVORK5CYII='));

        $this->actingAs($this->admin)->post('/admin/artifacts', [
            'title' => 'Test Manuscript', 'description' => 'A complete artifact description.',
            'category_id' => $category->id, 'tags' => [$tag->id], 'image' => $image,
        ])->assertRedirect(route('admin.artifacts.index'));

        $artifact = Artifact::firstOrFail();
        $this->assertSame($this->admin->id, $artifact->user_id);
        $this->assertTrue($artifact->tags->contains($tag));
        Storage::disk('public')->assertExists($artifact->image);

        $this->put("/admin/artifacts/{$artifact->id}", [
            'title' => 'Updated Manuscript', 'description' => 'Updated description.',
            'category_id' => $category->id, 'tags' => [],
        ])->assertRedirect();
        $this->assertDatabaseHas('artifacts', ['title' => 'Updated Manuscript']);

        $this->delete("/admin/artifacts/{$artifact->id}")->assertRedirect();
        $this->assertDatabaseMissing('artifacts', ['id' => $artifact->id]);
    }

    public function test_event_crud_and_profile_update_work(): void
    {
        $this->actingAs($this->admin)->post('/admin/events', [
            'title' => 'Test Event', 'description' => 'Historical details.', 'date_or_period' => '1121', 'location' => 'Didgori',
        ])->assertRedirect();
        $event = HistoricalEvent::firstOrFail();

        $this->put("/admin/events/{$event->id}", [
            'title' => 'Updated Event', 'description' => 'Updated details.', 'date_or_period' => '1121', 'location' => 'Georgia',
        ])->assertRedirect();
        $this->delete("/admin/events/{$event->id}")->assertRedirect();
        $this->assertDatabaseMissing('historical_events', ['id' => $event->id]);

        $this->put('/profile', ['bio' => 'Curator profile'])->assertRedirect(route('profile.edit'));
        $this->assertDatabaseHas('profiles', ['user_id' => $this->admin->id, 'bio' => 'Curator profile']);
    }

    public function test_category_with_artifacts_cannot_be_deleted(): void
    {
        $category = Category::create(['name' => 'Weapon']);
        Artifact::create(['title' => 'Sword', 'description' => 'Historic sword', 'category_id' => $category->id, 'user_id' => $this->admin->id]);

        $this->actingAs($this->admin)->delete("/admin/categories/{$category->id}")
            ->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
