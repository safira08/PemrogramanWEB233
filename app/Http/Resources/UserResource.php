<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'collection_detail' => $this->whenPivotLoaded('collections', function () {
                return [
                    'notes' => $this->pivot->notes,
                    'added_at' => $this->pivot->created_at?->toISOString(),
                ];
            }),
            'media_collection' => MediaResource::collection($this->whenLoaded('media')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
