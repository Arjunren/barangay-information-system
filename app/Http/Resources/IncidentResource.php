<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncidentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'title' => $this->title, 'description' => $this->description, 'location' => $this->location, 'occurred_at' => $this->occurred_at, 'status' => $this->status, 'reported_by' => $this->reported_by, 'created_at' => $this->created_at];
    }
}
