<?php

namespace App\Http\Requests\Auth;

use App\Models\PlanSubscription;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class LoginRequest extends FormRequest
{
    protected PlanSubscription $subscription;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticateToken() : string
    {
        $this->ensureIsNotRateLimited();

        if (! $token = Auth::attempt($this->only('email', 'password'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        return $token;
    }

    /**
     * Attempt to authenticateTool the request's credentials.
     *
     * @return PlanSubscription
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticateTool() : PlanSubscription
    {
        $this->ensureIsNotRateLimited();

        if (!$this->checkLicense()) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'license' => __('auth.failed'),
            ]);
        }

        if (!$this->checkMaSp()) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'license' => __('auth.failed'),
            ]);
        }

        if (! $token = Auth::attempt($this->only('email', 'password'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $this->ensureIsLogedOutTool();
        JWTAuth::setToken($token);
        $this->subscription->loginTool($token)->save();

        RateLimiter::clear($this->throttleKey());

        return $this->subscription;
    }

    public function checkLicense() : bool
    {
        if ($this->has('license') && !is_null($this->license)) {
            if ($subscription = app(PlanSubscription::class)->withoutGlobalScopes()->forLicense($this->license)->first()) {
                $this->subscription = $subscription;
                return $subscription->isActive();
            }
        }
        return false;
    }

    public function checkMaSp() : bool
    {
        if ($this->checkHash()) {
            if ($this->subscription->product()->first()->prefix_key === $this->masp) {
                return true;
            }
        }
        return false;
    }

    public function checkHash() : bool
    {
        if ($this->has('masp') && !is_null($this->masp)) {
            try {
                $hash = hash_hmac('sha256', $this->masp.'.'.$this->license, env('TOOL_SECRET', "abc@#$%^&&$"));
                if ($hash
                    && !is_null($this->hash)
                    && strtoupper($hash) === $this->hash) {
                    return true;
                }

            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    public function ensureIsLogedOutTool()
    {
        try {
            if ($token = $this->subscription->token) {
                JWTAuth::setToken($token)->invalidate(true);
            }
        } catch (\Exception $e) {
            //throw $e;
        }

        return $this;
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('email')).'|'.$this->ip();
    }
}
