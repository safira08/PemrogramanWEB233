<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * 
 * API Resource untuk memformat data User ke format JSON yang konsisten dan rapi.
 * Memuat koleksi media pribadi secara kondisional via whenLoaded('media').
 */
class UserResource extends JsonResource
{
    /**
     * Mengubah resource User menjadi representasi array untuk respons JSON.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Atribut identitas pengguna
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,

            // Informasi tambahan dari tabel pivot jika diakses dari relasi
            'collection_detail' => $this->whenPivotLoaded('media_user', function () {
                return [
                    'notes' => $this->pivot->notes,
                    'added_at' => $this->pivot->created_at?->toISOString(),
                ];
            }),

            // Daftar koleksi media milik pengguna (hanya disertakan jika sudah dieager load)
            'media_collection' => MediaResource::collection($this->whenLoaded('media')),

            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
