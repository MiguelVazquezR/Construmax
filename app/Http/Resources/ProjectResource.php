<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'folio' => 'PR-' . str_pad($this->id, 4, "0", STR_PAD_LEFT),
            'id' => $this->id,
            'name' => $this->name,
            'service_type' => $this->service_type,
            'group' => $this->projectGroup,
            'description' => $this->description,
            'is_strict' => $this->is_strict,
            'is_internal' => $this->is_internal,
            'currency' => $this->currency,
            'invoice_type' => $this->invoice_type,
            'budget' => $this->budget,
            'address' => $this->address,
            'contact' => $this->whenLoaded('contact'),
            'company' => $this->whenLoaded('company'),
            'opportunity' =>$this->whenLoaded('opportunity'),
            'user' => $this->whenLoaded('user'),
            'users' => $this->whenLoaded('users'),
            'owner' => $this->whenLoaded('owner'),
            'tasks' => TaskResource::collection($this->whenLoaded('tasks')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'media' => $this->getMedia()->all(),
            'start_date' => $this->start_date?->isoFormat('DD MMM YYYY'),
            'limit_date' => $this->limit_date?->isoFormat('DD MMM YYYY'),
            'raw_limit_date' => $this->limit_date->toDateString(),
            'finished_at' => $this->finished_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'created_at' => $this->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            'raw_created_at' => $this->created_at?->toDateString(),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM, YYYY h:mm A'),
        ];
    }
}
