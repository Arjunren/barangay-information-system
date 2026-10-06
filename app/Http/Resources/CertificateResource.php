<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'resident' => new ResidentResource($this->whenLoaded('resident')), 'certificate_type' => $this->certificate_type, 'purpose' => $this->purpose, 'status' => $this->status, 'remarks' => $this->remarks, 'processed_at' => $this->processed_at, 'created_at' => $this->created_at];
    }
}
