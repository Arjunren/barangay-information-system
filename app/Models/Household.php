<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['household_number', 'address', 'zone', 'created_by'])]
class Household extends Model
{
    public function residents()
    {
        return $this->hasMany(Resident::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
