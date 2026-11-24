<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 * 
 * Model Eloquent yang merepresentasikan tabel 'users'.
 * Digunakan untuk mengelola data akun pengguna di FlipPlay.
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Casting atribut otomatis.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi Many-to-Many ke Media.
     * Satu Pengguna dapat memiliki banyak Media di dalam koleksi pribadinya.
     * Menggunakan tabel pivot konvensional 'media_user'.
     */
    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_user')
            ->withPivot(['id', 'notes']) // Mengambil kolom catatan di pivot
            ->withTimestamps();          // Mengambil waktu penambahan ke koleksi
    }
}
