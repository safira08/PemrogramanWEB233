<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class FlipPlayApiTest
 * 
 * Pengujian otomatis (Unit & Feature Test) untuk seluruh endpoint REST API FlipPlay Modul 4.
 * Menggunakan data studi kasus resmi FlipPlay (Anime, Drama, Manga, Light Novel).
 * Mencakup Skenario Berhasil (Happy Path) dan Skenario Gagal (Failure Path/Validation).
 */
class FlipPlayApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup awal sebelum setiap pengujian: menjalankan migrasi dan seeder database.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * [Happy Path] Menguji apakah endpoint GET /api/media mengembalikan daftar seluruh media dengan status 200.
     */
    public function test_can_list_all_media(): void
    {
        $response = $this->getJson('/api/media');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'title', 'type', 'genre', 'release_year', 
                        'duration', 'description', 'created_at', 'updated_at'
                    ]
                ]
            ]);
    }

    /**
     * [Happy Path] Menguji query filter pencarian media berdasarkan judul.
     */
    public function test_can_filter_media_by_search_query(): void
    {
        $response = $this->getJson('/api/media?title=Attack');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Attack on Titan']);
    }

    /**
     * [Happy Path] Menguji penambahan media baru dengan data valid (Jujutsu Kaisen, type: anime) menghasilkan status HTTP 201 Created.
     */
    public function test_can_create_new_media_happy_path(): void
    {
        $payload = [
            'title' => 'Jujutsu Kaisen',
            'type' => 'anime',
            'genre' => 'Action / Supernatural',
            'release_year' => 2020,
            'duration' => 24,
            'description' => 'Yuji Itadori bergabung dengan sekolah penyihir Jujutsu untuk memusnahkan kutukan berbahaya.',
        ];

        $response = $this->postJson('/api/media', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Jujutsu Kaisen']);

        // Pastikan data tersimpan secara fisik di database tabel media
        $this->assertDatabaseHas('media', ['title' => 'Jujutsu Kaisen']);
    }

    /**
     * [Failure Path] Menguji validasi Form Request menolak pembuatan media dengan judul kosong (HTTP 422).
     */
    public function test_cannot_create_media_with_empty_title(): void
    {
        $payload = [
            'title' => '',
            'type' => 'anime',
            'genre' => 'Action',
        ];

        $response = $this->postJson('/api/media', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * [Failure Path] Menguji validasi Form Request menolak pembuatan media dengan judul duplikat (HTTP 422).
     */
    public function test_cannot_create_duplicate_media_title(): void
    {
        $payload = [
            'title' => 'Attack on Titan', // Judul ini sudah ada di DatabaseSeeder
            'type' => 'anime',
            'genre' => 'Action',
        ];

        $response = $this->postJson('/api/media', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * [Happy Path] Menguji detail satu media beserta daftar pengguna yang mengoleksinya (eager loading).
     */
    public function test_can_show_media_detail_with_collected_users(): void
    {
        $aot = Media::where('title', 'Attack on Titan')->firstOrFail();

        $response = $this->getJson("/api/media/{$aot->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Attack on Titan'])
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

    /**
     * [Happy Path] Menguji pembaruan data media melalui endpoint PUT /api/media/{id} (HTTP 200).
     */
    public function test_can_update_media(): void
    {
        $media = Media::first();

        $response = $this->putJson("/api/media/{$media->id}", [
            'title' => 'Attack on Titan: The Final Season',
            'type' => 'anime',
            'genre' => 'Action / Dark Fantasy',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Attack on Titan: The Final Season']);

        $this->assertDatabaseHas('media', ['title' => 'Attack on Titan: The Final Season']);
    }

    /**
     * [Happy Path] Menguji penghapusan media dari katalog FlipPlay (HTTP 200).
     */
    public function test_can_delete_media(): void
    {
        $media = Media::where('title', 'Solo Leveling')->firstOrFail();

        $response = $this->deleteJson("/api/media/{$media->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    /**
     * [Happy Path] Menguji custom action menambahkan media ke koleksi pengguna (HTTP 201).
     */
    public function test_can_add_media_to_user_collection(): void
    {
        $user = User::where('email', 'ahmad@flipplay.test')->firstOrFail();
        $media = Media::where('title', 'Moving')->firstOrFail();

        $response = $this->postJson("/api/users/{$user->id}/collections", [
            'media_id' => $media->id,
            'notes' => 'Mau nonton pas liburan',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['message' => "Media '{$media->title}' berhasil ditambahkan ke dalam koleksi {$user->name}."]);

        $this->assertDatabaseHas('media_user', [
            'user_id' => $user->id,
            'media_id' => $media->id,
        ]);
    }

    /**
     * [Failure Path] Menguji penolakan penambahan media yang sama dua kali ke koleksi pengguna (HTTP 409 Conflict).
     */
    public function test_cannot_add_same_media_twice_to_user_collection(): void
    {
        $user = User::where('email', 'ahmad@flipplay.test')->firstOrFail();
        $aot = Media::where('title', 'Attack on Titan')->firstOrFail();

        // Attack on Titan sudah ada di koleksi Ahmad melalui DatabaseSeeder
        $response = $this->postJson("/api/users/{$user->id}/collections", [
            'media_id' => $aot->id,
        ]);

        // Berdasarkan Modul 4 hal 26 (Tabel 4.2), status code untuk data duplikat adalah 409 Conflict
        $response->assertStatus(409)
            ->assertJsonFragment(['message' => 'Media sudah ada di koleksi']);
    }

    /**
     * [Happy Path] Menguji menampilkan daftar koleksi media milik pengguna tertentu (HTTP 200).
     */
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

    /**
     * [Happy Path] Menguji custom action menghapus media dari koleksi pengguna (HTTP 200).
     */
    public function test_can_remove_media_from_user_collection(): void
    {
        $user = User::where('email', 'ahmad@flipplay.test')->firstOrFail();
        $onePiece = Media::where('title', 'One Piece')->firstOrFail();

        $response = $this->deleteJson("/api/users/{$user->id}/collections/{$onePiece->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => "Media '{$onePiece->title}' berhasil dihapus dari koleksi {$user->name}."]);

        $this->assertDatabaseMissing('media_user', [
            'user_id' => $user->id,
            'media_id' => $onePiece->id,
        ]);
    }
}
