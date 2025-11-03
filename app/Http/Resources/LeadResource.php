<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
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
            'status' => $this->status,
            'pipeline' => $this->pipeline,
            'contact' => [
                'id' => $this->contact->id ?? null,
                'name' => $this->contact->name ?? null,
                'email' => $this->contact->email ?? null,
            ],
            'created_at' => $this->created_at->toDateTimeString(),
        ];

    }
}
