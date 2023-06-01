<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RolesEnum;
use App\Models\Invoice;
use App\Models\Kpi;
use Carbon\CarbonPeriod;
use Doctrine\DBAL\Exception;
use Illuminate\Support\Collection;
use App\Actions\InvoiceStoreAction;
use App\Actions\InvoiceUpdateAction;
use App\Helpers\Traits\DateRangeHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\InvoiceCollection;
use App\Http\Resources\PlanSubscriptionResource;
use App\Traits\Files\FileHandler;
use Illuminate\Support\Carbon;

class InvoiceController extends Controller
{
    use DateRangeHelper, FileHandler;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InvoiceRequest $request)
    {
        $invoices = QueryBuilder::for(Invoice::class)
            ->allowedIncludes(['user', 'subscriber', 'plan', 'product', 'planSubscription', 'bank', 'files'])
            ->allowedFilters([
                'his', 'transaction', 'user.email', 'subscriber.email',
                'plan.name', 'plan.tag', 'bank.name_bank','bank.short_name','bank.code',
                AllowedFilter::trashed(),
                AllowedFilter::exact('status'),
                AllowedFilter::scope('startBetween'),
            ])
            ->paginate($request->per_page ?? 10)
            ->appends($request->all());

        return $this->respondSuccess(
            new InvoiceCollection($invoices)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceRequest $request)
    {
        if ($subscription = app(InvoiceStoreAction::class)->execute($request->validated())) {
            return $this->respondSuccess(
                new PlanSubscriptionResource($subscription->loadMissing('subscriber'))
            );
        }

        return $this->respondError('Lỗi không xác định!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(InvoiceRequest $request, Invoice $invoice)
    {
        $with = [];
        if ($request->has('include')) {
            $with = collect(explode(',', $request->include))->filter()->all();
        }
        return $this->respondSuccess(new InvoiceResource($invoice->loadMissing($with)));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(InvoiceRequest $request, Invoice $invoice)
    {
        if(Auth::user()->hasRoleAdmin()){
            return $this->respondSuccess(new InvoiceResource(
                app(InvoiceUpdateAction::class)->execute($invoice, $request->validated())
            ));
        }
        return $this->respondForbidden();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(InvoiceRequest $request, Invoice $invoice)
    {
        if ($invoice->isRunning()) {
            return $this->respondFailedValidation(
                __('trans.delete_fail', ['resource', 'Hóa đơn '.$invoice->code]) . '. Key đang hoạt động!'
            );
        }
        if ($invoice->delete()) {
            return $this->respondOk(__('trans.delete_success', ['resource', 'Hóa đơn '.$invoice->code]));
        }
        return $this->respondError(__('trans.delete_fail', ['resource', 'Hóa đơn '.$invoice->code]));
    }

    public function getDashboardRevenue(InvoiceRequest $request)
    {
        return $this->respondSuccess([
            'yearTotalCountryRevenue' => app(Invoice::class)->withoutGlobalScopes()->getTotalRevenueOf('thisYear'),
            'nowLastMonthTotalCountryRevenue' => app(Invoice::class)->getTotalRevenueOf('nowLastMonth'),
            'thisMonthTotalCountryRevenue' => app(Invoice::class)->getTotalRevenueOf('thisMonth'),
            'totalKpiYear' => app(Kpi::class)->getTotal('thisYear'),
            'totalKpiMonth' => app(Kpi::class)->getTotal('thisMonth'),
            'thisDayPreviousMonth' => app(Invoice::class)->paided()->getTotalRevenueOf('thisDayPreviousMonth'),
            'today' => [
                'todayTotalCountryRevenue' => app(Invoice::class)->getTotalRevenueOf('today'),
                'avgDayTotalKpiMonth' => app(Kpi::class)->getTotal('thisMonth') / Carbon::now()->daysInMonth,
            ],
        ]);
    }

    public function getDashboardChart(InvoiceRequest $request)
    {
        return $this->respondSuccess(
            array_merge(
                $this->buildChartMonth(app(Invoice::class)->getSumEveryDayOfMonth($request->month, $request->year)),
                $this->buildChartMonth(app(Invoice::class)->getSumEveryDayOfMonth($request->month, $request->year, true), 'paided'),
            )
        );
    }

    private function buildChartMonth(Collection $data, string $key = 'current')
    {
        $chart = [];
        $labels = [];
        [$start, $end] =$this->getStartAndEndOf(request()->month, request()->year);
        while ($start->lte($end)) {
            $day = $start->format('d-m-Y');
            $labels[] = $day;

            if ($data->contains('sum_date', $day)) {
                $data->filter(function($date) use ($day, &$chart){
                    if ($day === $date->sum_date) {
                        $chart[] = $date->sum_total;
                    }
                });
            } else {
                $chart[] = 0;
            }

            $start->addDay();
        }
        return ['labels' => $labels, $key => $chart];
    }

}
