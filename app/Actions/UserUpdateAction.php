<?php

namespace App\Actions;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\QueueableAction\QueueableAction;

class UserUpdateAction
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
    public function execute(User $user, array $dataUser)
    {
        if ('hungtq@phanmemmkt.vn' === $user->email) {
            return $user;
        }
        if (!empty($dataUser['password'])) {
            $dataUser['password'] = Hash::make($dataUser['password']);
        }
        DB::beginTransaction();
        try {
            if($user->fill($dataUser)->save()){
                if (!empty($dataUser['roles'])) {
                    $user->syncRoles($dataUser['roles']);
                    $role = Role::findByName($dataUser['roles']);
                    $user->givePermissionTo($role->getAllPermissions());
                }
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
        }
        return $user;
    }
}
