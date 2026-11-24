<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel 'media'.
 * Tabel ini merepresentasikan entitas Media pada FlipPlay (film, serial, musik, podcast).
 */
return new class extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel media di database.
     */
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            // Kolom ID auto-increment sebagai primary key
            $table->id();

            // Judul media (wajib unik di sistem FlipPlay)
            $table->string('title')->unique();

            // Tipe media, misalnya: movie, series, music, podcast
            $table->string('type');

            // Genre media, misalnya: Sci-Fi, Rock, Horror
            $table->string('genre');

            // Tahun rilis media (opsional/nullable)
            $table->integer('release_year')->nullable();

            // Durasi media dalam satuan menit (opsional/nullable)
            $table->integer('duration')->nullable();

            // Deskripsi atau sinopsis singkat media (opsional/nullable)
            $table->text('description')->nullable();

            // created_at dan updated_at untuk mencatat waktu pembuatan & perubahan data
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi (rollback) dengan menghapus tabel media.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
