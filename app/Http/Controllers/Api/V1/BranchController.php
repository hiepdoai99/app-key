<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BranchRequest;
use App\Http\Resources\BranchCollection;
use App\Http\Resources\BranchResource;
use App\Models\Branch;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class BranchController extends Controller
{
    public function index(BranchRequest $request)
    {
        $branchs = QueryBuilder::for(Branch::class)
            ->allowedIncludes(['teams', 'users', 'kpis'])
            ->allowedFilters([
                'name', 'slug',
                AllowedFilter::trashed(),
            ])
            ->allowedAppends('revenue', 'revenue_approve', 'revenue_today', 'ranges')
            ->paginate($request->per_page ?? 10)
            ->appends($request->all());

        return $this->respondSuccess(new BranchCollection($branchs));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BranchRequest $request)
    {
        return $this->respondCreated(
            new BranchResource(Branch::create($request->validated()))
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(BranchRequest $request, Branch $branch)
    {
        return $this->respondSuccess(new BranchResource($branch));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(BranchRequest $request, Branch $branch)
    {
        $branch->fill($request->validated())->save();
        return $this->respondSuccess(new BranchResource($branch));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy(BranchRequest $request, Branch $branch)
    {
        return $this->respondError(__('trans.delete_fail',['resource',  'Branch '.$branch->name]));
        if ($branch->delete()) {
            return $this->respondOk(__('trans.delete_success', ['resource', 'Branch '.$branch->name]));
        }
        return $this->respondError(__('trans.delete_fail',['resource',  'Branch '.$branch->name]));
    }
}
