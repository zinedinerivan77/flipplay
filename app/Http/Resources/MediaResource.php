<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'genre' => $this->genre,
            'year' => $this->year,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'users' => UserResource::collection($this->whenLoaded('users')),
        ];
    }
}
