<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlipPlayApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_crud_and_title_search(): void
    {
        $created = $this->postJson('/api/media', [
            'title' => 'Blue Horizon',
            'type' => 'anime',
            'genre' => 'Adventure',
            'year' => 2024,
        ])->assertCreated()
            ->assertJsonPath('data.title', 'Blue Horizon');

        $mediaId = $created->json('data.id');

        $this->getJson('/api/media?search=Blue')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $mediaId);

        $this->getJson('/api/media/'.$mediaId)
            ->assertOk()
            ->assertJsonPath('data.title', 'Blue Horizon')
            ->assertJsonPath('data.users', []);

        $this->patchJson('/api/media/'.$mediaId, [
            'title' => 'Blue Horizon: Updated',
            'description' => 'A demo description.',
        ])->assertOk()
            ->assertJsonPath('data.title', 'Blue Horizon: Updated')
            ->assertJsonPath('data.description', 'A demo description.');

        $this->deleteJson('/api/media/'.$mediaId)->assertNoContent();
        $this->getJson('/api/media/'.$mediaId)->assertNotFound();
    }

    public function test_media_validation_rejects_empty_and_duplicate_titles(): void
    {
        $this->postJson('/api/media', [
            'title' => '',
            'type' => 'anime',
            'genre' => 'Adventure',
            'year' => 2024,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);

        Media::create([
            'title' => 'Existing Title',
            'type' => 'drama',
            'genre' => 'Drama',
            'year' => 2022,
        ]);

        $this->postJson('/api/media', [
            'title' => 'Existing Title',
            'type' => 'anime',
            'genre' => 'Adventure',
            'year' => 2024,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    public function test_users_can_manage_a_media_collection_and_duplicate_add_is_rejected(): void
    {
        $userResponse = $this->postJson('/api/users', [
            'name' => 'Ayu',
            'email' => 'ayu@example.test',
            'password' => 'password123',
        ])->assertCreated()
            ->assertJsonMissingPath('data.password');

        $userId = $userResponse->json('data.id');
        $media = Media::create([
            'title' => 'Collection Sample',
            'type' => 'manga',
            'genre' => 'Fantasy',
            'year' => 2023,
        ]);

        $this->postJson("/api/users/{$userId}/collection/{$media->id}")
            ->assertCreated()
            ->assertJsonPath('data.id', $media->id);

        $this->getJson("/api/users/{$userId}/collection")
            ->assertOk()
            ->assertJsonPath('data.0.id', $media->id);

        $this->getJson("/api/media/{$media->id}")
            ->assertOk()
            ->assertJsonPath('data.users.0.id', $userId);

        $this->postJson("/api/users/{$userId}/collection/{$media->id}")
            ->assertStatus(409)
            ->assertJsonPath('message', 'Media is already in this user\'s collection.');

        $this->deleteJson("/api/users/{$userId}/collection/{$media->id}")
            ->assertNoContent();

        $this->deleteJson("/api/users/{$userId}/collection/{$media->id}")
            ->assertNotFound();
    }

    public function test_user_validation_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'ayu@example.test']);

        $this->postJson('/api/users', [
            'name' => 'Another Ayu',
            'email' => 'ayu@example.test',
            'password' => 'password123',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }
}
