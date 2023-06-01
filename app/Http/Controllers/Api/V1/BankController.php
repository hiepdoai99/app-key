<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\BankNameEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\BankRequest;
use App\Http\Resources\BankCollection;
use App\Http\Resources\BankResource;
use App\Models\Bank;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class BankController extends Controller
{
    public function index(BankRequest $request)
    {
        $banks = QueryBuilder::for(Bank::class)
            ->allowedIncludes(['invoices'])
            ->allowedFilters([
                'account_holder', 'account_number','name_bank','branch',
                AllowedFilter::scope('searchName'),
                AllowedFilter::trashed(),
            ])
            ->paginate($request->per_page ?? 10)
            ->appends($request->all());

        return $this->respondSuccess(new BankCollection($banks));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BankRequest $request)
    {
        return $this->respondCreated(
            new BankResource(Bank::create($request->validated()))
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function show(BankRequest $request, Bank $bank)
    {
        return $this->respondSuccess(new BankResource($bank));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(BankRequest $request, Bank $bank)
    {
        $bank->fill($request->validated())->save();
        return $this->respondSuccess(new BankResource($bank));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankRequest $request, Bank $bank)
    {
        return $this->respondError(__('trans.delete_fail',['resource',  'Bank '.$bank->name]));
        if ($bank->delete()) {
            return $this->respondOk(__('trans.delete_success', ['resource', 'Bank '.$bank->name]));
        }
        return $this->respondError(__('trans.delete_fail',['resource',  'Bank '.$bank->name]));
    }

    public function listBank()
    {
        return $this->respondSuccess(json_decode((string)BankNameEnum::data(), true));
    }
}
