<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Class DatabaseSeeder
 * 
 * Seeder untuk mengisi data awal (dummy data) FlipPlay resmi ke dalam database.
 * Menggunakan 4 jenis media resmi FlipPlay: anime, drama, manga, dan light novel.
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

        // 2. Buat Contoh Katalog Media Resmi FlipPlay (Anime, Drama, Manga, Light Novel)
        $media1 = Media::create([
            'title' => 'Attack on Titan',
            'type' => 'anime',
            'genre' => 'Action / Dark Fantasy',
            'release_year' => 2013,
            'duration' => 24,
            'description' => 'Umat manusia berjuang bertahan hidup di balik tembok raksasa dari ancaman Titan pemakan manusia.',
        ]);

        $media2 = Media::create([
            'title' => 'Moving',
            'type' => 'drama',
            'genre' => 'Sci-Fi / Action',
            'release_year' => 2023,
            'duration' => 50,
            'description' => 'Kisah anak-anak SMA dengan kekuatan super warisan orang tua mereka yang mantan agen rahasia.',
        ]);

        $media3 = Media::create([
            'title' => 'One Piece',
            'type' => 'manga',
            'genre' => 'Adventure / Fantasy',
            'release_year' => 1997,
            'duration' => 1100,
            'description' => 'Petualangan Monkey D. Luffy dan bajak laut Topi Jerami mengarungi Grand Line mencari harta karun One Piece.',
        ]);

        $media4 = Media::create([
            'title' => 'Solo Leveling',
            'type' => 'light novel',
            'genre' => 'Action / Fantasy',
            'release_year' => 2016,
            'duration' => 270,
            'description' => 'Perjalanan Sung Jin-Woo dari hunter terlemah menjadi hunter terkuat di dunia melalui sistem quest misterius.',
        ]);

        // 3. Hubungkan data relasi Many-to-Many ke tabel pivot 'media_user' (Koleksi Pengguna)
        // User 1 (Ahmad Fauzi) mengoleksi Attack on Titan dan One Piece
        $user1->media()->attach($media1->id, [
            'notes' => 'Anime favorit dengan plot twist terbaik',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user1->media()->attach($media3->id, [
            'notes' => 'Manga legendaris wajib dibaca',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // User 2 (Siti Nurhaliza) mengoleksi Moving dan Solo Leveling
        $user2->media()->attach($media2->id, [
            'notes' => 'Drama korea action terbaik tahun 2023',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user2->media()->attach($media4->id, [
            'notes' => 'Light novel seru pas maraton akhir pekan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
