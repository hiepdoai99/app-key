<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\RoleUpdateAction;
use App\Models\Role;
use App\Http\Requests\RoleRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Spatie\QueryBuilder\QueryBuilder;

class RoleController extends Controller
{
    /**
     * Display a listing of the role.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RoleRequest $request)
    {
        $roles = QueryBuilder::for(Role::class)
            ->allowedIncludes(['permissions'])
            ->allowedFilters(['name'])
            ->get();

        return $this->respondSuccess(
            // TODO bo with permissions vi khong can thiet render
            RoleResource::collection($roles)
        );
    }

    /**
     * Store a newly created role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        return $this->respondForbidden(
            $request->name ?? 'Không có quyền thêm Roles'
        );
    }

    /**
     * Display the specified role.
     *
     * @param  Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(RoleRequest $request, Role $role)
    {
        return $this->respondSuccess(
            new RoleResource($role->loadMissing('permissions'))
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, Role $role)
    {
        $role = app(RoleUpdateAction::class)->execute($role, $request->validated());
        if ($role instanceof Role) {
            return $this->respondSuccess(
                new RoleResource($role->loadMissing('permissions'))
            );
        }
        return $this->respondError($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(RoleRequest $request, Role $role)
    {
        return $this->respondForbidden('Không có quyền xóa Roles');
    }
}
