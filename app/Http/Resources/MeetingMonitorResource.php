<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingMonitorResource extends JsonResource
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
            'meeting_date' => $this->meeting_date?->isoFormat('DD MMM YYYY'),
            'meeting_date_raw' => $this->meeting_date,
            'time' => Carbon::parse($this->time)->format('h:i A'),
            'meeting_via' => $this->meeting_via,
            'location' => $this->location,
            'contact_phone' => $this->contact_phone,
            'contact_name' => $this->contact_name,
            'description' => $this->description,
            'branch' => $this->branch,
            'customer' => $this->whenLoaded('customer'),
            'opportunity' => OpportunityResource::make($this->whenLoaded('opportunity')),
            'seller' => $this->whenLoaded('seller'),
            'clientMonitor' => $this->whenLoaded('clientMonitor'),
            'contact' => $this->whenLoaded('contact'),
            'participants' => $this->participants,
            'created_at' => $this->created_at?->isoFormat('DD MMM YYYY'),
            'updated_at' => $this->updated_at?->isoFormat('DD MMM YYYY'),
        ];
    }
}
