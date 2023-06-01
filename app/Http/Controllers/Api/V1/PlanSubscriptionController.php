<?php

namespace App\Http\Controllers\Api\V1;

use App\Exports\PlanSubscriptionExport;
use App\Models\Plan;
use App\Models\User;
use App\Models\PlanSubscription;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlanSubscriptionRequest;
use App\Http\Resources\PlanSubscriptionResource;
use App\Http\Resources\PlanSubscriptionCollection;
use App\Includes\InvoiceInclude;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;
use Maatwebsite\Excel\Facades\Excel;


class PlanSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PlanSubscriptionRequest $request)
    {
        $subscription = QueryBuilder::for(PlanSubscription::class)
            ->allowedIncludes([
                'plan', 'subscriber', 'product', 'invoices', 'invoices.user','invoices.bank','invoices.files'
            ])
            ->allowedFilters(['license', 'name', 'plan_id','product_id','starts_at','ends_at','id', 'invoices.trased',
                AllowedFilter::scope('active'),
                AllowedFilter::scope('trial'),
                AllowedFilter::scope('whereEmail'),
                AllowedFilter::scope('whereMemberEmail'),
                AllowedFilter::scope('remainingDay'),
                AllowedFilter::scope('startBetween'),
                AllowedFilter::scope('offline'),
                AllowedFilter::exact('invoices.status'),

            ])
            ->allowedSorts('ends_at', 'trial_ends_at')
            ->paginate($request->per_page ?? 10)
            ->appends($request->all());

        return $this->respondSuccess(
            new PlanSubscriptionCollection($subscription)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PlanSubscriptionRequest $request)
    {
        return $this->respondForbidden();
        $data = $request->validated();
        $subscriber = User::find($data['subscriber_id']);
        $plan = Plan::find($data['plan_id']);
        return $this->respondCreated(
            $subscriber->newSubscription('main', $plan, $data['name'], $data['description'])
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PlanSubscription  $planSubscription
     * @return \Illuminate\Http\Response
     */
    public function show(PlanSubscriptionRequest $request, PlanSubscription $plansubscription)
    {
        return $this->respondForbidden();
        return $this->respondSuccess(new PlanSubscriptionResource($plansubscription));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PlanSubscription  $planSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(PlanSubscriptionRequest $request, PlanSubscription $planSubscription)
    {
        return $this->respondForbidden();
        $planSubscription->fill($request->validated())->save();
        return $this->respondSuccess(new PlanSubscriptionResource($planSubscription));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PlanSubscription  $planSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(PlanSubscriptionRequest $request, PlanSubscription $planSubscription)
    {
        return $this->respondForbidden();
        if ($planSubscription->delete()) {
            return $this->respondOk(__('trans.delete_success', ['resource', 'đăng ký '.$planSubscription->name]));
        }
        return $this->respondError(__('trans.delete_fail', ['resource', 'đăng ký '.$planSubscription->name]));
    }

    public function getDashboardsTags()
    {
        return $this->respondSuccess(app(PlanSubscription::class)->getDashboardsTags());
    }
    public function export(PlanSubscriptionRequest $request)
    {
        $subscription = QueryBuilder::for(PlanSubscription::with([
                'plan', 'subscriber', 'product', 'invoices', 'invoices.user','invoices.bank',
            ])->active())
            ->allowedFilters(['license', 'name', 'plan_id','product_id','starts_at','ends_at','id', 'invoices.trased',
                AllowedFilter::scope('trial'),
                AllowedFilter::scope('whereEmail'),
                AllowedFilter::scope('remainingDay'),

            ])
            ->defaultSort('ends_at')
            ->allowedSorts('ends_at', 'start_at')
            ->get();

        return Excel::download(new PlanSubscriptionExport($subscription), 'plansubscription.xlsx');
    }
    public function exportTrial(PlanSubscriptionRequest $request)
    {
        $subscription = QueryBuilder::for(PlanSubscription::with([
                'plan', 'subscriber', 'product', 'invoices', 'invoices.user','invoices.bank',
            ])->trial())
            ->allowedFilters(['license', 'name', 'plan_id','product_id','starts_at','ends_at','id', 'invoices.trased',
                AllowedFilter::scope('whereEmail'),
            ])
            ->defaultSort('trial_ends_at')
            ->allowedSorts('trial_ends_at')
            ->get();

        return Excel::download(new PlanSubscriptionExport($subscription), 'plansubscriptionExportTrial.xlsx');
    }
    public function exportExpire(PlanSubscriptionRequest $request)
    {
        $subscription = QueryBuilder::for(PlanSubscription::with([
            'plan', 'subscriber', 'product', 'invoices', 'invoices.user','invoices.bank',
        ])->remainingDay(7))
            ->allowedFilters(['license', 'name', 'plan_id','product_id','starts_at','ends_at','id', 'invoices.trased',
                AllowedFilter::scope('active'),
                AllowedFilter::scope('whereEmail'),
            ])
            ->defaultSort('ends_at')
            ->get();

        return Excel::download(new PlanSubscriptionExport($subscription), 'plansubscriptionExportExpire.xlsx');
    }

}
