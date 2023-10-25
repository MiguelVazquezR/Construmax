<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $priority = '';

        if ($this->priority == 'Baja') {
            $priority = [
                'label' => 'Baja',
                'color_border' => 'border-[#87CEEB]'
            ];
        } else if ($this->priority == 'Media') {
            $priority = [
                'label' => 'Media',
                'color_border' => 'border-[#F2C940]'
            ];
        } else if ($this->priority == 'Alta') {
            $priority = [
                'label' => 'Alta',
                'color_border' => 'border-[#FB2A2A]'
            ];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'department' => $this->department,
            'priority' => $priority,
            'status' => $this->status,
            'is_paused' => $this->is_paused,
            'reminder' => $this->reminder,
            'project' => $this->whenLoaded('project'),
            'user' => $this->whenLoaded('user'),
            'users' => $this->whenLoaded('users'),
            'project_owner' => $this->project->owner,
            'comments' => $this->whenLoaded('comments'),
            'media' => $this->getMedia()->all(),
            'start_date' => $this->start_date?->isoFormat('DD MMM YYYY'),
            'start_date_raw' => $this->start_date?->toDateString(),
            'limit_date' => $this->limit_date?->isoFormat('DD MMM YYYY'),
            'limit_date_raw' => $this->limit_date?->toDateString(),
            'start_time' => $this->start_time,
            'limit_time' => $this->limit_time,
            'created_at' => $this->created_at?->isoFormat('DD MMM YYYY'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM YYYY'),
        ];
    }
}
