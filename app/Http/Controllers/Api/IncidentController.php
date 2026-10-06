<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IncidentRequest;
use App\Http\Requests\IncidentStatusRequest;
use App\Http\Resources\IncidentResource;
use App\Models\IncidentReport;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Gate;

class IncidentController extends Controller
{
    public function index()
    {
        $user = request()->user();
        $query = IncidentReport::query();
        if (! $user->isStaff()) {
            $query->where('reported_by', $user->id);
        }
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        return IncidentResource::collection($query->latest('occurred_at')->paginate(min(max((int) request('per_page', 20), 1), 100)));
    }

    public function store(IncidentRequest $request): IncidentResource
    {
        $model = IncidentReport::create([...$request->validated(), 'reported_by' => $request->user()->id, 'status' => 'open']);
        ActivityLogger::record($request->user(), 'incident.reported', $model);

        return new IncidentResource($model);
    }

    public function show(IncidentReport $incident): IncidentResource
    {
        Gate::authorize('view', $incident);

        return new IncidentResource($incident);
    }

    public function update(IncidentStatusRequest $request, IncidentReport $incident): IncidentResource
    {
        Gate::authorize('update', $incident);
        $incident->update($request->validated());
        ActivityLogger::record($request->user(), 'incident.status_changed', $incident, $request->validated());

        return new IncidentResource($incident);
    }
}
