<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Traits\HtqJwtResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthenticatedJwtController extends Controller
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
        return $this->respondWithToken($request->authenticateToken());
    }

    public function verifyToken(Request $request)
    {
        if ($request->token) {
            try {
                return $this->respondSuccess(
                    new UserResource(JWTAuth::parseToken()->authenticate()->load(['roles', 'permissions']))
                );
            } catch (\Exception $e) {
                return $this->respondUnAuthenticated($e->getMessage());
            }
        }
        return $this->respondFailedValidation(__('validation.required', ['attribute', 'Token']));
    }

    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    /**
     * Destroy an authenticated session.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        Auth::logout();

        return $this->respondNoContent();
    }
}
