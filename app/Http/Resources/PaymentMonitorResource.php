<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMonitorResource extends JsonResource
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
            'paid_at' => $this->paid_at?->isoFormat('DD MMMM YYYY, H:mm A'),
            'paid_at_raw' => $this->paid_at,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'concept' => $this->concept,
            'notes' => $this->notes,
            'branch' => $this->branch,
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'media' => $this->getMedia()->all(),
            'seller' => $this->whenLoaded('seller'),
            'opportunity' => OpportunityResource::make($this->whenLoaded('opportunity')),
            'contact' => $this->whenLoaded('contact'),
            'customer' => $this->whenLoaded('customer'),
            'clientMonitor' => $this->whenLoaded('clientMonitor'),
            'created_at' => $this->created_at?->isoFormat('DD MMMM YYYY'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMMM YYYY'),
        ];
    }
}
