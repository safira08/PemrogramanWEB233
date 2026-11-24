<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk tabel pivot 'media_user'.
 * Tabel ini menghubungkan entitas User dan Media dengan relasi Many-to-Many (belongsToMany).
 * Mengikuti konvensi Laravel: gabungan nama kedua model dalam bentuk singular alfabetis (media_user).
 */
return new class extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel pivot media_user.
     */
    public function up(): void
    {
        Schema::create('media_user', function (Blueprint $table) {
            // Primary key id untuk baris pivot
            $table->id();

            // Foreign key ke tabel users (jika user dihapus, relasi koleksi ikut terhapus)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Foreign key ke tabel media (jika media dihapus, relasi koleksi ikut terhapus)
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();

            // Catatan pribadi pengguna terhadap media dalam koleksinya (opsional)
            $table->text('notes')->nullable();

            // Timestamps untuk mencatat waktu kapan media ditambahkan ke koleksi
            $table->timestamps();

            // Unique constraint gabungan agar seorang user tidak bisa menambahkan media yang sama dua kali ke koleksinya
            $table->unique(['user_id', 'media_id']);
        });
    }

    /**
     * Batalkan migrasi (rollback) dengan menghapus tabel media_user.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_user');
    }
};

