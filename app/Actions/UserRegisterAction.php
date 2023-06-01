<?php

namespace App\Actions;

use App\Enums\InvoiceStatusEnum;
use App\Enums\RolesEnum;
use App\Enums\UserTypesEnum;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\QueueableAction\QueueableAction;

class UserRegisterAction
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
    public function execute(array $dataUser)
    {
        $dataUser['password'] = Hash::make($dataUser['password']);
        if (Auth::hasUser() && (string)UserTypesEnum::member() !== $dataUser['user_type']) {
            $dataUser['creator_id'] = Auth::id();
        }

        $user = new User();
        DB::beginTransaction();
        try {
            if ($user->fill($dataUser)->save() && !empty($dataUser['roles'])) {
                $user->assignRole($dataUser['roles']);
                $role = Role::findByName($dataUser['roles']);
                $user->givePermissionTo($role->getAllPermissions());

                if (request()->is('*/tool/*')) {
                    $product = Product::query()->where('prefix_key', $dataUser['masp'])->first();
                    $plan = $product->plans()->threeDayTrial()->first();
                    $subscription = app(InvoiceStoreAction::class)->execute([
                        'user_id' => User::query()->role((string)RolesEnum::root())->first()->id,
                        'subscriber_id' => $user->id,
                        'product_id' => $product->id,
                        'plan_id' => $plan->id,
                        'total' => $plan->price,
                        'code' => 1,
                        'status' => (string)InvoiceStatusEnum::unpaid(),
                        'his' => $dataUser['his'],
                        'note' => 'Khách dùng thử từ App',
                    ]);

                }

                DB::commit();
                return $subscription ?? $user;

            }
        } catch (\Exception $e) {
            DB::rollBack();
        }
        return $user ?? false;
    }
}
