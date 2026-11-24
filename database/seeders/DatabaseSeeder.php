<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Class DatabaseSeeder
 * 
 * Seeder untuk mengisi data awal (dummy data) yang realistis ke dalam database FlipPlay.
 * Dijalankan dengan perintah: `php artisan migrate:fresh --seed`
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan proses seeding database.
     */
    public function run(): void
    {
        // 1. Buat Contoh Akun Pengguna FlipPlay
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

        // 2. Buat Contoh Katalog Media FlipPlay (Film, Musik, Serial, Podcast)
        $media1 = Media::create([
            'title' => 'Inception',
            'type' => 'movie',
            'genre' => 'Sci-Fi / Action',
            'release_year' => 2010,
            'duration' => 148,
            'description' => 'A thief who steals corporate secrets through the use of dream-sharing technology.',
        ]);

        $media2 = Media::create([
            'title' => 'Bohemian Rhapsody',
            'type' => 'music',
            'genre' => 'Classic Rock',
            'release_year' => 1975,
            'duration' => 6,
            'description' => 'Masterpiece lagu rock legendaris dari band Queen.',
        ]);

        $media3 = Media::create([
            'title' => 'Stranger Things Season 1',
            'type' => 'series',
            'genre' => 'Mystery / Sci-Fi',
            'release_year' => 2016,
            'duration' => 50,
            'description' => 'Misteri hilangnya seorang anak dan kemunculan gadis dengan kekuatan psikis rahasia.',
        ]);

        $media4 = Media::create([
            'title' => 'Podcast Lentera Malam',
            'type' => 'podcast',
            'genre' => 'Horror / Storytelling',
            'release_year' => 2021,
            'duration' => 45,
            'description' => 'Cerita horor nyata dan investigasi urban legend nusantara.',
        ]);

        // 3. Hubungkan data relasi Many-to-Many ke tabel pivot 'media_user' (Koleksi Pengguna)
        // User Ahmad Fauzi mengoleksi Inception dan Bohemian Rhapsody
        $user1->media()->attach($media1->id, [
            'notes' => 'Film favorit sepanjang masa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user1->media()->attach($media2->id, [
            'notes' => 'Lagu wajib playlist santai',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // User Siti Nurhaliza mengoleksi Inception dan Stranger Things
        $user2->media()->attach($media1->id, [
            'notes' => 'Rekomendasi dari teman kuliah',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user2->media()->attach($media3->id, [
            'notes' => 'Tontonan maraton akhir pekan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
