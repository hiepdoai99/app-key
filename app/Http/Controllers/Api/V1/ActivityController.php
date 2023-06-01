<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Activity;
use Illuminate\Http\Request;
use App\Enums\SubjectTypesEnum;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\ActivityCollection;
use App\Http\Resources\ActivityResource;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $activities = QueryBuilder::for(Activity::class)
            ->allowedIncludes(['subject', 'causer'])
            ->allowedFilters([
                'event', 'subject_type',
            ])
            ->paginate($request->per_page ?? 10)
            ->appends($request->all());

        return $this->respondSuccess(new ActivityCollection($activities));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \Spatie\Activitylog\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function show(Activity $activity)
    {
        return $this->respondSuccess(new ActivityResource(Activity::find(1)));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Spatie\Activitylog\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Activity $activity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Spatie\Activitylog\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $activity)
    {
        //
    }

    public function list(Request $request)
    {
        return $this->respondSuccess(SubjectTypesEnum::toValues());
    }
}
