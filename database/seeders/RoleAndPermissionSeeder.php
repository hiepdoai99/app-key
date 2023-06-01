<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\RolesEnum;
use App\Enums\PermissionsEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\App;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();

        // create permissions
        $permissions = collect(PermissionsEnum::toValues())->map(function ($permission) {
            return [
                'name' => $permission,
                'guard_name' => config('auth.defaults.guard'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });
        Permission::insert($permissions->toArray());

        // create roles
        $roles = collect(RolesEnum::toValues())->map(function ($role) {
            return [
                'name' => $role,
                'guard_name' => config('auth.defaults.guard'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });
        Role::insert($roles->toArray());

        // create roles and assign created permissions
        $rootUser = User::where('email', 'hungtq@phanmemmkt.vn')->first();
        $rootUser->assignRole((string)RolesEnum::root());
        $allPermissions = array_column($permissions->toArray(), 'name');
        Role::findByName((string)RolesEnum::root())
            ->givePermissionTo($allPermissions);
        Role::findByName((string)RolesEnum::admin())
            ->givePermissionTo($allPermissions);

        $rootUser->givePermissionTo($allPermissions);

        // dev seeder
        if (! App::environment('production')) {
            $users = User::query()->member()->get();
            $users->map(function($user) {
                $user->assignRole((string)RolesEnum::sales());
            });
        }
    }
}
