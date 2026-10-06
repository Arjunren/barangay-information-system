<?php

namespace App\Policies;

use App\Models\IncidentReport;
use App\Models\User;

class IncidentReportPolicy
{
    public function view(User $user, IncidentReport $incident): bool
    {
        return $user->isStaff() || $incident->reported_by === $user->id;
    }

    public function update(User $user, IncidentReport $incident): bool
    {
        return $user->isStaff();
    }
}
