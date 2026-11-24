<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MediaResource
 * 
 * API Resource untuk memformat data Media menjadi struktur respons JSON yang konsisten.
 * Menerapkan kondisional relasi 'whenLoaded' dan detail tabel pivot 'whenPivotLoaded'.
 */
class MediaResource extends JsonResource
{
    /**
     * Mengubah resource Media menjadi representasi array untuk JSON.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Atribut dasar entitas Media
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'genre' => $this->genre,
            'release_year' => $this->release_year,
            'duration' => $this->duration,
            'description' => $this->description,

            // Informasi pivot tabel jika model diakses melalui relasi Many-to-Many
            'collection_detail' => $this->whenPivotLoaded('media_user', function () {
                return [
                    'notes' => $this->pivot->notes,
                    'added_at' => $this->pivot->created_at?->toISOString(),
                ];
            }),

            // Relasi pengguna yang mengoleksi media ini (hanya dimuat jika eager loaded menggunakan load('users'))
            'collected_by_users' => UserResource::collection($this->whenLoaded('users')),

            // Waktu pencatatan data dalam format ISO string
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
