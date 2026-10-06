<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResidentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'resident_number' => $this->resident_number, 'first_name' => $this->first_name, 'last_name' => $this->last_name, 'birth_date' => $this->birth_date?->toDateString(), 'sex' => $this->sex, 'civil_status' => $this->civil_status, 'phone' => $this->phone, 'registered_voter' => $this->registered_voter, 'household' => $this->whenLoaded('household', fn () => new HouseholdResource($this->household)), 'created_at' => $this->created_at];
    }
}
