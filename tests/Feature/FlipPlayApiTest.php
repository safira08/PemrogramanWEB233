<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlipPlayApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed default initial data
        $this->seed();
    }

    public function test_can_list_all_media(): void
    {
        $response = $this->getJson('/api/media');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'type', 'genre', 'release_year', 'duration', 'description', 'created_at', 'updated_at']
                ]
            ]);
    }

    public function test_can_filter_media_by_search_query(): void
    {
        $response = $this->getJson('/api/media?search=Inception');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Inception']);
    }

    public function test_can_create_new_media_happy_path(): void
    {
        $payload = [
            'title' => 'Interstellar',
            'type' => 'movie',
            'genre' => 'Sci-Fi / Adventure',
            'release_year' => 2014,
            'duration' => 169,
            'description' => 'A team of explorers travel through a wormhole in space in an attempt to ensure humanity survival.',
        ];

        $response = $this->postJson('/api/media', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Interstellar']);

        $this->assertDatabaseHas('media', ['title' => 'Interstellar']);
    }

    public function test_cannot_create_media_with_empty_title(): void
    {
        $payload = [
            'title' => '',
            'type' => 'movie',
            'genre' => 'Action',
        ];

        $response = $this->postJson('/api/media', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_cannot_create_duplicate_media_title(): void
    {
        $payload = [
            'title' => 'Inception', // Sudah ada di seeder
            'type' => 'movie',
            'genre' => 'Action',
        ];

        $response = $this->postJson('/api/media', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_can_show_media_detail_with_collected_users(): void
    {
        $inception = Media::where('title', 'Inception')->firstOrFail();

        $response = $this->getJson("/api/media/{$inception->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Inception'])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'collected_by_users' => [
                        '*' => ['id', 'name', 'email']
                    ]
                ]
            ]);
    }

    public function test_can_update_media(): void
    {
        $media = Media::first();

        $response = $this->putJson("/api/media/{$media->id}", [
            'title' => 'Inception Updated Edition',
            'type' => 'movie',
            'genre' => 'Sci-Fi Thriller',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Inception Updated Edition']);

        $this->assertDatabaseHas('media', ['title' => 'Inception Updated Edition']);
    }

    public function test_can_delete_media(): void
    {
        $media = Media::where('title', 'Podcast Lentera Malam')->firstOrFail();

        $response = $this->deleteJson("/api/media/{$media->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    public function test_can_add_media_to_user_collection(): void
    {
        $user = User::where('email', 'ahmad@flipplay.test')->firstOrFail();
        $media = Media::where('title', 'Podcast Lentera Malam')->firstOrFail();

        $response = $this->postJson("/api/users/{$user->id}/collections", [
            'media_id' => $media->id,
            'notes' => 'Mau didengar pas malam jumat',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['message' => "Media '{$media->title}' berhasil ditambahkan ke dalam koleksi {$user->name}."]);

        $this->assertDatabaseHas('collections', [
            'user_id' => $user->id,
            'media_id' => $media->id,
        ]);
    }

    public function test_cannot_add_same_media_twice_to_user_collection(): void
    {
        $user = User::where('email', 'ahmad@flipplay.test')->firstOrFail();
        // Inception sudah ada di koleksi Ahmad melalui DatabaseSeeder
        $inception = Media::where('title', 'Inception')->firstOrFail();

        $response = $this->postJson("/api/users/{$user->id}/collections", [
            'media_id' => $inception->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'Gagal: Media ini sudah ada di dalam koleksi Anda.']);
    }

    public function test_can_view_user_collection(): void
    {
        $user = User::where('email', 'ahmad@flipplay.test')->firstOrFail();

        $response = $this->getJson("/api/users/{$user->id}/collections");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'collections' => [
                    '*' => ['id', 'title', 'type', 'genre']
                ]
            ]);
    }

    public function test_can_remove_media_from_user_collection(): void
    {
        $user = User::where('email', 'ahmad@flipplay.test')->firstOrFail();
        $inception = Media::where('title', 'Inception')->firstOrFail();

        $response = $this->deleteJson("/api/users/{$user->id}/collections/{$inception->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => "Media '{$inception->title}' berhasil dihapus dari koleksi {$user->name}."]);

        $this->assertDatabaseMissing('collections', [
            'user_id' => $user->id,
            'media_id' => $inception->id,
        ]);
    }
}
