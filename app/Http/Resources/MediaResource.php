<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'genre' => $this->genre,
            'release_year' => $this->release_year,
            'duration' => $this->duration,
            'description' => $this->description,
            'collection_detail' => $this->whenPivotLoaded('collections', function () {
                return [
                    'notes' => $this->pivot->notes,
                    'added_at' => $this->pivot->created_at?->toISOString(),
                ];
            }),
            'collected_by_users' => UserResource::collection($this->whenLoaded('users')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
