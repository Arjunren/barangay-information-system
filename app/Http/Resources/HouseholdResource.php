<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HouseholdResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'household_number' => $this->household_number, 'address' => $this->address, 'zone' => $this->zone, 'residents_count' => $this->whenCounted('residents'), 'created_at' => $this->created_at];
    }
}
