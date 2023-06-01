<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Permission;
use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use App\Models\Role;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RolesPermissionUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permissions update and sync all';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // create permissions
        $allPermissions = PermissionsEnum::toValues();
        $permissions = collect($allPermissions)->map(function ($permission) {
            return [
                'name' => $permission,
                'guard_name' => config('auth.defaults.guard'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });
        $roleRoot = (string)RolesEnum::root();

        DB::beginTransaction();

        try {
            Permission::upsert(
                $permissions->all(),
                ['name', 'guard_name', 'created_at', 'updated_at'],
            );
            // Role Root sync all permissions
            Role::findByName($roleRoot)->syncPermissions($allPermissions);

            // User Root sync all permissions
            if ($users = User::query()->role($roleRoot)->get()) {
                $users->map(function($user) use ($allPermissions){
                    $user->syncPermissions($allPermissions);
                });
            }
            DB::commit();
            return 'Update Root User Permission Success!';
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return 'Update Root User Permission Fail!';
    }
}
