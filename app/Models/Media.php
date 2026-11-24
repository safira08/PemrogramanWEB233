<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Media
 * 
 * Model Eloquent yang merepresentasikan tabel 'media'.
 * Digunakan untuk mengelola data item media di FlipPlay.
 */
class Media extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit di database.
     */
    protected $table = 'media';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (mass assignment).
     */
    protected $fillable = [
        'title',
        'type',
        'genre',
        'release_year',
        'duration',
        'description',
    ];

    /**
     * Casting otomatis tipe data kolom saat diakses dari database.
     */
    protected function casts(): array
    {
        return [
            'release_year' => 'integer',
            'duration' => 'integer',
        ];
    }

    /**
     * Relasi Many-to-Many ke Pengguna (User).
     * Satu Media dapat disimpan di koleksi oleh banyak Pengguna.
     * Menggunakan tabel pivot konvensional 'media_user'.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'media_user')
            ->withPivot(['id', 'notes']) // Menyertakan kolom pivot tambahan
            ->withTimestamps();          // Menyertakan created_at & updated_at di tabel pivot
    }
}
