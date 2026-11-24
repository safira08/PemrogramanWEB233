<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Pengguna FlipPlay
        $user1 = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@flipplay.test',
            'password' => Hash::make('password123'),
        ]);

        $user2 = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@flipplay.test',
            'password' => Hash::make('password123'),
        ]);

        // 2. Buat Contoh Media FlipPlay
        $media1 = Media::create([
            'title' => 'Inception',
            'type' => 'movie',
            'genre' => 'Sci-Fi / Action',
            'release_year' => 2010,
            'duration' => 148,
            'description' => 'A thief who steals corporate secrets through dream-sharing technology.',
        ]);

        $media2 = Media::create([
            'title' => 'Bohemian Rhapsody',
            'type' => 'music',
            'genre' => 'Classic Rock',
            'release_year' => 1975,
            'duration' => 6,
            'description' => 'Masterpiece song by legendary British rock band Queen.',
        ]);

        $media3 = Media::create([
            'title' => 'Stranger Things Season 1',
            'type' => 'series',
            'genre' => 'Mystery / Sci-Fi',
            'release_year' => 2016,
            'duration' => 50,
            'description' => 'When a young boy vanishes, a small town uncovers a mystery involving secret experiments.',
        ]);

        $media4 = Media::create([
            'title' => 'Podcast Lentera Malam',
            'type' => 'podcast',
            'genre' => 'Horror / Storytelling',
            'release_year' => 2021,
            'duration' => 45,
            'description' => 'Cerita horor nyata dan investigasi urban legend nusantara.',
        ]);

        // 3. Tambahkan ke koleksi pengguna (pivot)
        $user1->media()->attach($media1->id, ['notes' => 'Film favorit sepanjang masa', 'created_at' => now(), 'updated_at' => now()]);
        $user1->media()->attach($media2->id, ['notes' => 'Lagu wajib playlist santai', 'created_at' => now(), 'updated_at' => now()]);

        $user2->media()->attach($media1->id, ['notes' => 'Rekomendasi dari teman', 'created_at' => now(), 'updated_at' => now()]);
        $user2->media()->attach($media3->id, ['notes' => 'Nonton maraton akhir pekan', 'created_at' => now(), 'updated_at' => now()]);
    }
}
