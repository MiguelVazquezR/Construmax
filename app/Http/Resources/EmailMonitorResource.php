<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmailMonitorResource extends JsonResource
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
            'subject' => $this->subject,
            'content' => $this->content,
            'contact_name' => $this->contact_name,
            'contact_email' => $this->contact_email,
            'branch' => $this->branch,
            'customer' => $this->whenLoaded('customer'),
            'opportunity' => OpportunityResource::make($this->whenLoaded('opportunity')),
            'seller' => $this->whenLoaded('seller'),
            'contact' => $this->whenLoaded('contact'),
            'clientMonitor' => $this->whenLoaded('clientMonitor'),
            'created_at' => $this->created_at?->isoFormat('DD MMM YYYY'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM YYYY'),
        ];
    }
}
