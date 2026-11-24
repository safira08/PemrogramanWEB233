<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'title',
        'type',
        'genre',
        'release_year',
        'duration',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'release_year' => 'integer',
            'duration' => 'integer',
        ];
    }

    /**
     * Relasi many-to-many ke Pengguna yang menyimpan media ini dalam koleksi.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'collections')
            ->withPivot(['id', 'notes'])
            ->withTimestamps();
    }
}
