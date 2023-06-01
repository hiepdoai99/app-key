<?php

namespace App\Actions;

use App\Models\Role;
use App\Models\User;
use App\Enums\PermissionsEnum;
use Illuminate\Support\Facades\DB;
use Spatie\QueueableAction\QueueableAction;

class RoleUpdateAction
{
    use QueueableAction;

    /**
     * Create a new action instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Prepare the action for execution, leveraging constructor injection.
    }

    /**
     * Execute the action.
     *
     * @return mixed
     */
    public function execute(Role $role, array $dataPermissions) : Role | string
    {
        $allPermissions = PermissionsEnum::toValues();
        $permissions = collect($dataPermissions['permissions'])
                            ->filter(function($permission) use ($allPermissions) {
                                return in_array($permission, $allPermissions);
                            })->all();

        DB::beginTransaction();
        try {
            // Reset cached roles and permissions
            app()[\Spatie\Permission\PermissionRegistrar::class]
                ->forgetCachedPermissions();

            $role->syncPermissions($permissions);
            User::role($role->name)->get()
                ->each(function($user) use ($permissions) {
                    $user->syncPermissions($permissions);
                });
            DB::commit();
            return $role;
        } catch (\Exception $e) {
            //throw $th;
            DB::rollBack();
            return $e->getMessage();
        }
    }
}
