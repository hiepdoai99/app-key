<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\UserRegisterAction;
use App\Actions\UserUpdateAction;
use App\Enums\UserTypesEnum;
use App\Exports\CustomerExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    /**
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(UserRequest $request)
    {
        $userBuilder = QueryBuilder::for(User::class)
            ->allowedAppends(['ranges', 'revenue', 'revenue_approve', 'revenue_last_month'])
            ->allowedIncludes(['roles', 'permissions', 'team', 'subscribers', 'sales', 'branch', 'memberInvoices', 'customerInvoices'])
            ->allowedFilters([
                'first_name', 'email', 'phone','status','team_id','branch_id', 'sales.email','id',
                AllowedFilter::scope('searchName'),
                AllowedFilter::scope('member'),
                AllowedFilter::scope('customer'),
                AllowedFilter::scope('role'),
                // TODO chi loc sale
                AllowedFilter::scope('isSales'),
                AllowedFilter::trashed(),
            ]);

        if (-1 == $request->per_page) {
            $users = $userBuilder->get();

            return $this->respondSuccess(
                UserResource::collection($users)
            );

        }

        $user = $userBuilder->paginate($request->per_page ?? 10)
                    ->appends($request->all());
        return $this->respondSuccess(
            new UserCollection($user)
        );

    }

    /**
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRequest $request)
    {
        if ($user = app(UserRegisterAction::class)->execute($request->validated())) {
            return $this->respondCreated(new UserResource($user->loadMissing(['team', 'branch'])));
        }
        return $this->respondError('Tao moi user that bai');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(UserRequest $request, User $user)
    {
        $with = [];
        if ($request->has('include')) {
            $with = collect(explode(',', $request->include))->filter()->all();
        }
        return $this->respondSuccess(new UserResource($user->loadMissing($with)));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        if ($user = app(UserUpdateAction::class)->execute($user, $request->validated())){
            return $this->respondSuccess(
                new UserResource($user)
            );
        }
        return $this->respondError('Loi cap nhat User');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserRequest $request, User $user)
    {
        if ($user->hasSubscriptions()) {
            return $this->respondFailedValidation('User '. $user->name . ' đang có key hoạt động!');
        }
        if ($user->delete()) {
            return $this->respondOk(__('trans.delete_success', ['resource', 'User '.$user->name]));
        }
        return $this->respondError(__('trans.delete_fail', ['resource', 'User '.$user->name]));
    }

    public function getUserType(UserRequest $request)
    {
        $types = collect(UserTypesEnum::toValues())->map(function($type){
            return (object) [
                'name' => __('trans.'.$type),
                'value' => $type,
            ];
        });
        return $this->respondSuccess($types);
    }
    public function export(Request $request)
    {
        $customer = QueryBuilder::for(User::with(['roles', 'permissions', 'team', 'subscribers', 'sales', 'branch'])->customer())
            ->allowedFilters([
                'first_name', 'email', 'phone','status','team_id','branch_id', 'sales.email','id',
                AllowedFilter::scope('searchName'),
                AllowedFilter::scope('role'),
                AllowedFilter::trashed(),
            ])->get();
        return Excel::download(new CustomerExport($customer), 'customer.xlsx');

    }
}
