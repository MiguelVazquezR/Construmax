<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'branches' => $this->branches,
            'currency' => $this->currency,
            'rfc' => $this->rfc,
            'zipcode' => $this->zipcode,
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'invoicing_method' => $this->invoicing_method,
            'payment_method' => $this->payment_method,
            'invoice_use' => $this->invoice_use,
            'user' => $this->whenLoaded('user'),
            'contacts' => $this->whenLoaded('contacts'),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'created_at' => $this->created_at?->isoFormat('DD MMMM YYYY'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMMM YYYY'),
        ];
    }
}
