<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'household_id', 'resident_number', 'first_name', 'last_name', 'birth_date', 'sex', 'civil_status', 'phone', 'registered_voter', 'created_by', 'updated_by'])]
class Resident extends Model
{
    protected function casts(): array
    {
        return ['birth_date' => 'date', 'registered_voter' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function certificateRequests()
    {
        return $this->hasMany(CertificateRequest::class);
    }
}
