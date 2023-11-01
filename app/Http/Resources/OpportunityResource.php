<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->priority == 'Baja') {
            $priority = [
                'label' => 'Baja',
                'color' => 'text-[#87CEEB]'
            ];
        } else if ($this->priority == 'Media') {
            $priority = [
                'label' => 'Media',
                'color' => 'text-orange-500'
            ];
        } else if ($this->priority == 'Alta') {
            $priority = [
                'label' => 'Alta',
                'color' => 'text-red-600'
            ];
        }

        return [
            'id' => $this->id,
            'folio' => 'OP-' . strtoupper(substr($this->name, 0, 3)) . '-' . str_pad($this->id, 3, '0', STR_PAD_LEFT),
            'name' => $this->name,
            'contact' => $this->whenLoaded('contact'),
            'lost_oportunity_razon' => $this->lost_oportunity_razon,
            'amount' => $this->amount,
            'status' => $this->status,
            'description' => $this->description,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'service_type' => $this->service_type,
            'customer_name' => $this->customer_name,
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'branch' => $this->branch,
            'probability' => $this->probability,
            'type_access_project' => $this->type_access_project,
            'priority' => $priority,
            'finished_at' => $this->finished_at?->isoFormat('DD MMMM YYYY'),
            'paid_at' => $this->paid_at?->isoFormat('DD MMMM YYYY'),
            'start_date' => $this->start_date?->isoFormat('DD MMMM YYYY'),
            'media' => $this->getMedia()->all(),
            'close_date' => $this->close_date?->isoFormat('DD MMMM YYYY'),
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'user' => $this->whenLoaded('user'),
            'users' => $this->whenLoaded('users'),
            'seller' => $this->whenLoaded('seller'),
            'clientMonitors' => ClientMonitorResource::collection($this->whenLoaded('clientMonitors')),
            'opportunityTasks' => OpportunityTaskResource::collection($this->whenLoaded('opportunityTasks')),
            // 'survey' => $this->whenLoaded('survey'),
            'created_at' => [
                'diffForHumans' => $this->created_at?->diffForHumans(),
                'isoFormat' => $this->created_at?->isoFormat('DD MMMM YYYY'),
            ],
            'updated_at' => $this->created_at?->diffForHumans(),
        ];
    }
}
