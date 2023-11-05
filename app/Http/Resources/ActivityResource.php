<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
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
            'description' => $this->description,
            'user' => $this->whenLoaded('user'),
            'opportunity' => $this->whenLoaded('opportunity'),
            'created_at' => $this->created_at?->isoFormat('DD MMMM YYYY, H:mm A'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMMM YYYY, H:mm A'),
        ];
    }
}
