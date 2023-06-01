<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanFeatureRequest;
use App\Http\Resources\PlanFeatureResource;
use App\Models\PlanFeature;
use Illuminate\Http\Request;

class PlanFeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PlanFeatureRequest $request)
    {
        return $this->respondSuccess(
            PlanFeatureResource::collection(PlanFeature::all())
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PlanFeatureRequest $request)
    {
        return $this->respondCreated(PlanFeature::create($request->validated()));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PlanFeature  $planFeature
     * @return \Illuminate\Http\Response
     */
    public function show(PlanFeatureRequest $request, PlanFeature $planFeature)
    {
        return $this->respondSuccess(new PlanFeatureResource($planFeature));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PlanFeature  $planFeature
     * @return \Illuminate\Http\Response
     */
    public function update(PlanFeatureRequest $request, PlanFeature $planFeature)
    {
        $planFeature->fill($request->validated());
        return $this->respondSuccess(new PlanFeatureResource($planFeature));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PlanFeature  $planFeature
     * @return \Illuminate\Http\Response
     */
    public function destroy(PlanFeatureRequest $request, PlanFeature $planFeature)
    {
        if ($planFeature->delete()) {
            return $this->respondOk(__('trans.delete_success', ['resource', 'kho '.$planFeature->name]));
        }
        return $this->respondError(__('trans.delete_fail', ['resource', 'kho '.$planFeature->name]));
    }
}
