<?php

namespace App\Models;

use App\Models\Plan;
use App\Enums\RolesEnum;
use App\Scopes\LatestScope;
use App\Enums\UserTypesEnum;
use Illuminate\Support\Carbon;
use App\Scopes\MemberUserScope;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use App\Traits\SetDateRangesQuery;
use App\Traits\SpatieLogsActivity;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use App\Helpers\Traits\DateRangeHelper;
use App\Scopes\LeaderScope;
use Bpuig\Subby\Traits\HasSubscriptions;
use Illuminate\Notifications\Notifiable;
use Bpuig\Subby\Services\SubscriptionPeriod;
use Illuminate\Database\Eloquent\SoftDeletes;
use Bpuig\Subby\Exceptions\DuplicateException;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Bpuig\Subby\Exceptions\InvalidPlanSubscription;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;
    use HasRoles, HasSubscriptions, SpatieLogsActivity;
    use DateRangeHelper, SetDateRangesQuery;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'last_name',
        'first_name',
        'email',
        'password',
        'user_type',
        'team_id',
        'creator_id',
        'phone',
        'branch_id',
        'point',
        'status',
        'code',
        'referral',
        'email_verified_at',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // protected $appends = ['name', 'revenue', 'revenue_approve', 'revenue_last_month'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'boolean',
        'user_type' => UserTypesEnum::class,
    ];

    public function getRules() : array
    {
        $model = request()->user;
        return [
            'last_name' => ['sometimes', 'required', 'string'],
            'first_name' => ['sometimes', 'required', 'string'],
            'email' => ['sometimes', 'required', 'email',
                Rule::unique('users', 'email')->where(function ($query) use($model) {
                    return $query->where('id', '!=', $model?->id);
                })
            ],
            'email_verified_at' => ['nullable'],
            'team_id' => ['nullable', 'integer', 'min:1', 'exists:teams,id'],
            'creator_id' => ['nullable', 'integer', 'min:1', 'exists:users,id'],
            'password' => ['sometimes', 'required', 'confirmed', Rules\Password::defaults()],
            'remember_token' => ['nullable'],
            'branch_id' => ['nullable'],
            'user_type' => ['nullable', 'enum:'.UserTypesEnum::class],
            'phone' => ['nullable', 'string', 'max:20'],
            'point' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'boolean'],
            'code' => ['nullable', 'string'],
            'referral' => ['nullable', 'string', 'max:10'],
            'roles' => ['nullable', 'enum:'.RolesEnum::class],
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'license'         => request()->has('license') ? request()->license : '',
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }

    public function getNameAttribute()
    {
        return $this->last_name . ' ' . $this->first_name;
    }

    public function getRevenueAttribute()
    {
        return $this->memberInvoices()->sumBetweenRanges($this->ranges);
    }

    public function getRevenueLastMonthAttribute()
    {
        return $this->memberInvoices()->paided()->getTotalRevenueOf('lastMonth');
    }

    public function getRevenueApproveAttribute()
    {
        return $this->memberInvoices()->paided()->sumBetweenRanges($this->ranges);
    }

    public function isMember(): bool
    {
        return $this->user_type === (string)UserTypesEnum::member();
    }

    public function hasRoleAdmin(): bool
    {
        return $this->hasAnyRole([(string)RolesEnum::root(), (string)RolesEnum::admin()]);
    }

    public function hasSubscriptions() : bool
    {
        return $this->activeSubscriptions()->count() > 0;
    }

    public function scopeMember($query)
    {
        return $query->where('user_type', (string)UserTypesEnum::member());
    }

    public function scopeCustomer($query)
    {
        $user = Auth::user();

        if ($user->hasAnyRole([
            (string)RolesEnum::root(), (string)RolesEnum::admin(), (string)RolesEnum::leader()
        ])) {
            return $query->where('user_type', (string)UserTypesEnum::customer())
                    ->orWhere('user_type', (string)UserTypesEnum::walk_in())
                    ->orWhere('user_type', (string)UserTypesEnum::guest());
        }
        return $query->where('creator_id', $user->id);
    }

    // TODO xu ly get role sales
    public function scopeIsSales($query)
    {
        return $query;
    }

    public function scopeSearchName($query, $search)
    {
        return $query->whereLike(['first_name', 'last_name'], $search);
    }

    public function newSubscription(?string $license, Invoice $invoice, Plan $plan, Product $product, ?Carbon $startDate = null)
    {
        $subscriptionPeriod = new SubscriptionPeriod($plan, $startDate ?? now());

        try {
            $subscription = $this->subscription($license);
        } catch (InvalidPlanSubscription $e) {

            $subscription = $this->subscriptions()->create([
                'tag' => $license,
                'name' => $plan->name,
                'description' => $product->name . ' ' . $plan->name,
                'plan_id' => $plan->id,
                'price' => $plan->price,
                'currency' => $plan->currency,
                'tier' => $plan->tier,
                'trial_interval' => $plan->trial_interval,
                'trial_period' => $plan->trial_period,
                'grace_interval' => $plan->grace_interval,
                'grace_period' => $plan->grace_period,
                'invoice_interval' => $plan->invoice_interval,
                'invoice_period' => $plan->invoice_period,
                'trial_ends_at' => $subscriptionPeriod->getTrialEndDate(),
                'starts_at' => $subscriptionPeriod->getStartDate(),
                'ends_at' => $subscriptionPeriod->getEndDate(),
                'product_id' => $product->id,
                'his' => $invoice->his,
                'online' => false,
                'license' => $license,
            ]);

            $subscription->syncPlanFeatures($plan);

            return $subscription;
        }

        throw new DuplicateException();
    }

    public function subscribers()
    {
        return $this->belongsToMany(User::class, 'invoices', 'user_id', 'subscriber_id');
    }

    public function sales()
    {
        return $this->belongsToMany(User::class, 'invoices', 'subscriber_id', 'user_id');
    }

    public function memberInvoices()
    {
        return $this->hasMany(Invoice::class, 'user_id');
    }

    public function customerInvoices()
    {
        return $this->hasMany(Invoice::class, 'subscriber_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function kpis()
    {
        return $this->hasMany(Kpi::class);
    }

    protected static function booted()
    {
        if (Auth::hasUser() && Auth::user()->isMember()) {
            static::addGlobalScope(new MemberUserScope);
            static::addGlobalScope(new LeaderScope);
        }
        static::addGlobalScope(new LatestScope);
    }
}
