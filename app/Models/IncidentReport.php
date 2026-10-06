<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['reported_by', 'title', 'description', 'location', 'occurred_at', 'status'])]
class IncidentReport extends Model
{
    protected function casts(): array
    {
        return ['occurred_at' => 'datetime'];
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
