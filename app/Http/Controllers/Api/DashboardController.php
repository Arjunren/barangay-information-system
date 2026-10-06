<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CertificateRequest;
use App\Models\Household;
use App\Models\IncidentReport;
use App\Models\Resident;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return response()->json(['data' => ['households' => Household::count(), 'residents' => Resident::count(), 'registered_voters' => Resident::where('registered_voter', true)->count(), 'pending_certificates' => CertificateRequest::where('status', 'pending')->count(), 'open_incidents' => IncidentReport::whereIn('status', ['open', 'investigating'])->count()]]);
    }
}
