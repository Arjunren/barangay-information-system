<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CertificateStoreRequest;
use App\Http\Requests\CertificateUpdateRequest;
use App\Http\Resources\CertificateResource;
use App\Models\CertificateRequest;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CertificateController extends Controller
{
    public function index()
    {
        $user = request()->user();
        $query = CertificateRequest::with('resident.household');
        if (! $user->isStaff()) {
            $query->whereHas('resident', fn ($q) => $q->where('user_id', $user->id));
        }
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        return CertificateResource::collection($query->latest()->paginate(min(max((int) request('per_page', 20), 1), 100)));
    }

    public function store(CertificateStoreRequest $request): CertificateResource
    {
        $user = $request->user();
        $residentId = $user->isStaff() ? $request->validated('resident_id') : $user->resident?->id;
        abort_unless($residentId, 422, 'A linked resident record is required');
        $model = CertificateRequest::create([...$request->safe()->only(['certificate_type', 'purpose']), 'resident_id' => $residentId]);
        ActivityLogger::record($user, 'certificate.requested', $model);

        return new CertificateResource($model->load('resident.household'));
    }

    public function show(CertificateRequest $certificate): CertificateResource
    {
        Gate::authorize('view', $certificate);

        return new CertificateResource($certificate->load('resident.household'));
    }

    public function update(CertificateUpdateRequest $request, CertificateRequest $certificate): CertificateResource
    {
        Gate::authorize('update', $certificate);
        $updated = DB::transaction(function () use ($request, $certificate) {
            $locked = CertificateRequest::lockForUpdate()->findOrFail($certificate->id);
            $next = $request->validated('status');
            $allowed = ['pending' => ['approved', 'rejected'], 'approved' => ['released']];
            abort_unless(in_array($next, $allowed[$locked->status] ?? [], true), 409, 'Invalid status transition');
            $locked->update(['status' => $next, 'remarks' => $request->validated('remarks'), 'processed_by' => $request->user()->id, 'processed_at' => now()]);
            ActivityLogger::record($request->user(), 'certificate.status_changed', $locked, ['status' => $next]);

            return $locked;
        });

        return new CertificateResource($updated->load('resident.household'));
    }
}
