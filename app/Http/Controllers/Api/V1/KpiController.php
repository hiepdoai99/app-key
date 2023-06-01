<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\KpiStoreAction;
use App\Enums\RolesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\KpiRequest;
use App\Http\Resources\KpiCollection;
use App\Http\Resources\KpiResource;
use App\Models\Kpi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class KpiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(KpiRequest $request)
    {
        $kpis = QueryBuilder::for(Kpi::class)
            ->allowedIncludes(['branch', 'team', 'user', 'user.team', 'user.branch'])
            ->allowedFilters([
                'name', 'target', 'start_at', 'end_at', 'branch.name', 'team.name', 'user.email', 'user.team.name'
            ])
            ->paginate($request->per_page ?? 10)
            ->appends($request->all());

        return $this->respondSuccess(new KpiCollection($kpis));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(KpiRequest $request)
    {
        if ($kpi = app(KpiStoreAction::class)->execute($request->validated())) {
            return $this->respondCreated($kpi);
        }
        return $this->respondError('Tạo mới Kpi thất bại');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Kpi  $kpi
     * @return \Illuminate\Http\Response
     */
    public function show(KpiRequest $request, Kpi $kpi)
    {
        $with = [];
        if ($request->has('include')) {
            $with = collect(explode(',', $request->include))->filter()->all();
        }
        return $this->respondSuccess(new KpiResource($kpi->loadMissing($with)));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kpi  $kpi
     * @return \Illuminate\Http\Response
     */
    public function update(KpiRequest $request, Kpi $kpi)
    {
        $kpi->fill($request->validated())->save();
        return $this->respondSuccess(new KpiResource($kpi));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Kpi  $kpi
     * @return \Illuminate\Http\Response
     */
    public function destroy(KpiRequest $request, Kpi $kpi)
    {
        if ($kpi->delete()) {
            return $this->respondOk(__('trans.delete_success', ['resource', 'Kpi '.$kpi->name]));
        }
        return $this->respondError(__('trans.delete_fail',['resource',  'Kpi '.$kpi->name]));
    }
}
