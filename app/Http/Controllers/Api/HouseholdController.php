<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HouseholdRequest;
use App\Http\Resources\HouseholdResource;
use App\Models\Household;
use App\Services\ActivityLogger;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HouseholdController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $size = min(max((int) request('per_page', 20), 1), 100);
        $query = Household::query()->withCount('residents');
        if ($q = trim((string) request('q'))) {
            $query->where(fn ($x) => $x->where('household_number', 'like', "%$q%")->orWhere('address', 'like', "%$q%"));
        }
        if ($zone = request('zone')) {
            $query->where('zone', $zone);
        }

        return HouseholdResource::collection($query->orderBy('household_number')->paginate($size));
    }

    public function store(HouseholdRequest $request): HouseholdResource
    {
        $model = Household::create([...$request->validated(), 'created_by' => $request->user()->id]);
        ActivityLogger::record($request->user(), 'household.created', $model);

        return new HouseholdResource($model);
    }

    public function show(Household $household): HouseholdResource
    {
        return new HouseholdResource($household->loadCount('residents'));
    }

    public function update(HouseholdRequest $request, Household $household): HouseholdResource
    {
        $household->update($request->validated());
        ActivityLogger::record($request->user(), 'household.updated', $household);

        return new HouseholdResource($household);
    }

    public function destroy(Household $household)
    {
        abort_if($household->residents()->exists(), 409, 'Household still has residents');
        $household->delete();
        ActivityLogger::record(request()->user(), 'household.deleted', $household);

        return response()->noContent();
    }
}
