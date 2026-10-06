<?php

namespace App\Policies;

use App\Models\CertificateRequest;
use App\Models\User;

class CertificateRequestPolicy
{
    public function view(User $user, CertificateRequest $request): bool
    {
        return $user->isStaff() || $request->resident?->user_id === $user->id;
    }

    public function update(User $user, CertificateRequest $request): bool
    {
        return $user->isStaff();
    }
}
