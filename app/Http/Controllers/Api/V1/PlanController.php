<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanRequest;
use App\Http\Resources\PlanCollection;
use App\Http\Resources\PlanFeatureResource;
use App\Http\Resources\PlanResource;
use App\Http\Resources\PlanSubscriptionResource;
use App\Models\Plan;
use Spatie\QueryBuilder\QueryBuilder;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PlanRequest $request)
    {
        $plans = QueryBuilder::for(Plan::class)
            ->allowedIncludes(['product', 'subscriptions'])
            ->allowedFilters(['product.name', 'name','id'])
            ->paginate($request->per_page ?? 10)
            ->appends($request->all());

        return $this->respondSuccess(
            new PlanCollection($plans)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PlanRequest $request)
    {
        return $this->respondCreated(
            new PlanResource(Plan::create($request->validated()))
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function show(PlanRequest $request, Plan $plan)
    {
        return $this->respondSuccess(new PlanResource($plan));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(PlanRequest $request, Plan $plan)
    {
        $plan->fill($request->validated())->save();
        return $this->respondSuccess(new PlanResource($plan));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(PlanRequest $request, Plan $plan)
    {
        if ($plan->delete()) {
            return $this->respondOk(__('trans.delete_success', ['resource','gói '.$plan->name]));
        }
        return $this->respondError(__('trans.delete_fail', ['resource','gói '.$plan->name]));
    }

    public function getFeatures(PlanRequest $request, Plan $plan)
    {
        return $this->respondSuccess(PlanFeatureResource::collection($plan->features()));
    }

    public function getSubscriptions(PlanRequest $request, Plan $plan)
    {
        return $this->respondSuccess(PlanSubscriptionResource::collection($plan->subscriptions()));
    }
}
