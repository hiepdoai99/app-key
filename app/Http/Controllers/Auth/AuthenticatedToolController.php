<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Enums\RolesEnum;
use Illuminate\Support\Str;
use App\Enums\UserTypesEnum;
use Illuminate\Http\Request;
use App\Traits\HtqJwtResponse;
use App\Models\PlanSubscription;
use App\Actions\UserRegisterAction;
use App\Actions\UserRegisterToolAction;
use App\Actions\UserRegisterToolValidateAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\PlanSubscriptionResource;

class AuthenticatedToolController extends Controller
{
    use HtqJwtResponse;
    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LoginRequest $request)
    {
        $subscription = $request->authenticateTool();
        return $this->respondWithTokenAndSubscription($subscription->token, $subscription);
    }

    public function register(RegisterRequest $request)
    {
        $dataUser = $request->validated();
        if (!app(UserRegisterToolValidateAction::class)->execute($dataUser)) {
            $dataUser['user_type'] = (string)UserTypesEnum::guest();
            $dataUser['roles'] = (string)RolesEnum::guest();

            if ($created = app(UserRegisterAction::class)->execute($dataUser)) {
                if ($created instanceof PlanSubscription) {
                    return $this->respondCreated(new PlanSubscriptionResource($created));
                }
                return $this->respondCreated(new UserResource($created));
            }
        }
        return $this->respondError('Tao moi khach hang that bai');
    }

    public function refresh(Request $request)
    {
        if ($subscription = PlanSubscription::forToken($request->bearerToken())->first()) {
            try {
                if ($token = Auth::refresh(true, true)) {
                    $subscription->loginTool($token)->save();
                    return $this->respondWithTokenAndSubscription($token, $subscription);
                }
            } catch (\Exception $e) {
                return $this->respondError($e->getMessage());
            }
        }

        return $this->respondError(__('auth.failed'));
    }

    public function getHisByLicense(Request $request)
    {
        if ($request->has('license')) {
            $license = $request->license;
            if ($subscription = PlanSubscription::withoutGlobalScopes()->forLicense($license)->first()) {
                return $this->respondWithTokenAndSubscription(Str::random(5), $subscription);
            }
        }
        return $this->respondError(__('auth.failed'));
    }

    /**
     * Destroy an authenticated session.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->has('license') && !is_null($request->license)) {
            if ($subscription = app(PlanSubscription::class)->withoutGlobalScopes()->forLicense($request->license)->first()) {
                $subscription->logoutTool()->save();
            }
        }

        Auth::logout();

        return $this->respondNoContent();
    }
}
