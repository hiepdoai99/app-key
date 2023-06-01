<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RolesEnum;
use App\Models\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use App\Http\Resources\PermissionResource;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->respondSuccess(
            PermissionResource::collection(Permission::all())
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function refresh()
    {
        if (Auth::hasUser() && Auth::user()->hasRoleAdmin()){
            return $this->respondOk(Artisan::call('permission:update'));
        }
        return $this->respondForbidden('Bạn không có quyền cập nhật Permission!');
    }

}
