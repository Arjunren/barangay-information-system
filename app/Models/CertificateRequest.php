<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['resident_id', 'certificate_type', 'purpose', 'status', 'remarks', 'processed_by', 'processed_at'])]
class CertificateRequest extends Model
{
    protected function casts(): array
    {
        return ['processed_at' => 'datetime'];
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
