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
                'color_border' => 'border-orange-500'
            ];
        } else if ($this->priority == 'Alta') {
            $priority = [
                'label' => 'Alta',
                'color_border' => 'border-red-600'
            ];
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'department' => $this->department,
            'priority' => $priority,
            'status' => $this->status,
            'is_paused' => $this->is_paused,
            'reminder' => $this->reminder,
            'project' => $this->whenLoaded('project'),
            'participants' => $this->whenLoaded('participants'),
            'user' => $this->whenLoaded('user'),
            'comments' => $this->whenLoaded('comments'),
            'media' => $this->getMedia()->all(),
            'start_date' => $this->start_date?->isoFormat('DD MMM YYYY'),
            'end_date' => $this->end_date?->isoFormat('DD MMM YYYY'),
            'created_at' => $this->created_at?->isoFormat('DD MMM YYYY'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM YYYY'),
        ];
    }
}
