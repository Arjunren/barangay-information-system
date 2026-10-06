<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResidentRequest;
use App\Http\Resources\ResidentResource;
use App\Models\Resident;
use App\Services\ActivityLogger;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ResidentController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $size = min(max((int) request('per_page', 20), 1), 100);
        $query = Resident::query()->with('household');
        if ($q = trim((string) request('q'))) {
            $query->where(fn ($x) => $x->where('resident_number', 'like', "%$q%")->orWhere('first_name', 'like', "%$q%")->orWhere('last_name', 'like', "%$q%"));
        }
        if (request()->has('registered_voter')) {
            $query->where('registered_voter', request()->boolean('registered_voter'));
        }

        return ResidentResource::collection($query->orderBy('last_name')->orderBy('first_name')->paginate($size));
    }

    public function store(ResidentRequest $request): ResidentResource
    {
        $data = $request->validated();
        $model = Resident::create([...$data, 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id]);
        ActivityLogger::record($request->user(), 'resident.created', $model);

        return new ResidentResource($model->load('household'));
    }

    public function show(Resident $resident): ResidentResource
    {
        return new ResidentResource($resident->load('household'));
    }

    public function me(): ResidentResource
    {
        $resident = request()->user()->resident()->with('household')->firstOrFail();

        return new ResidentResource($resident);
    }

    public function update(ResidentRequest $request, Resident $resident): ResidentResource
    {
        $resident->update([...$request->validated(), 'updated_by' => $request->user()->id]);
        ActivityLogger::record($request->user(), 'resident.updated', $resident);

        return new ResidentResource($resident->load('household'));
    }

    public function destroy(Resident $resident)
    {
        $resident->delete();
        ActivityLogger::record(request()->user(), 'resident.deleted', $resident);

        return response()->noContent();
    }
}
