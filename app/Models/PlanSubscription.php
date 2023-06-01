<?php

namespace App\Models;

use App\Helpers\Traits\DateRangeHelper;
use App\Scopes\LatestScope;
use App\Scopes\LeaderScope;
use Illuminate\Support\Carbon;
use App\Traits\HtqScopeExclude;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Scopes\PlanSubscriptionUserScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Bpuig\Subby\Models\PlanSubscription as SubbyPlanSubscription;
use Illuminate\Support\Str;

class PlanSubscription extends SubbyPlanSubscription
{
    use HasFactory, HtqScopeExclude, DateRangeHelper;

    protected $appends = ['is_old', 'remaing_day'];

    public function getFillable()
    {
        $fillable = parent::getFillable();
        $fillable = array_merge(
            $fillable,
            ['online', 'product_id', 'license', 'token', 'his']
        );
        return $fillable;
    }

    public function getCasts()
    {
        $casts = parent::getCasts();
        $casts['online'] = 'boolean';
        return $casts;
    }

    public function getRules() : array
    {
        $model = request()->planSubscription;
        $rules = parent::getRules();
        $rules = array_merge(
            $rules,
            [
                'invoice_period' => ['sometimes', 'integer', 'min:1', 'max:1000000000'],
                'token' => ['nullable', 'string'],
                'online' => ['nullable', 'boolean'],
                'product_id' => ['sometimes', 'required', 'integer', 'exists:products,id'],
                'his' => ['sometimes', 'required', 'string'],
                'license' => [
                    'sometimes',
                    'required',
                    'string',
                    Rule::unique(config('subby.tables.plan_subscriptions'), 'license')->where(function ($query) use($model){
                        return $query->where('id', '!=', $model?->id);
                    }),
                ]
            ]
        );
        return $rules;
    }

    // Relationship
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // Appends
    public function getIsOldAttribute()
    {
        $isOld = false;
        if ($this->starts_at) {
            $isOld = $this->starts_at->subMinutes(10) > Carbon::now() && !$this->isOnTrial();
        }
        return $this->attributes['is_old'] = $isOld;
    }

    public function getRemaingDayAttribute()
    {
        return $this->getSubscriptionPeriodRemainingUsageIn('day');
    }

    // Scope
    public function scopeTrial($query)
    {
        return $query->where('trial_ends_at', '>=', now());
    }
    public function scopeNotTrial($query)
    {
        return $query->whereNotNull('ends_at');
    }

    public function scopeActive($query)
    {
        return $query->where('ends_at', '>=', now());
    }

    public function scopeWithInvoice($query)
    {
        $table = $this->getTable();
        return $query->join('invoices', function ($join) use ($table){
            $join->on($table.'.plan_id', '=', 'invoices.plan_id')
                ->on($table.'.his', '=', 'invoices.his');
        });
    }

    public function scopeForLicense($query, $license)
    {
        return $query->where('license', $license);
    }

    public function scopeForToken($query, $token)
    {
        return $query->where('token', $token);
    }
    public function scopeWhereMemberEmail($query,$email)
    {
        $user = User::where('email',$email)->first();
        return $query->withInvoice()->WhereIn('invoices.user_id', [$user->id]);
    }
    public function scopeWhereEmail($query,$email)
    {
        $user = User::where('email',$email)->first();
        $table = $this->getTable();
        return $query->withInvoice()->WhereIn('invoices.user_id', [$user->id])
            ->orWhereIn($table.'.subscriber_id',[$user->id]);
    }
    public function scopeRemainingDay($query,$remainingDay)
    {
        return $query->where('ends_at', '<', Carbon::now()->addDays($remainingDay));
    }
    public function scopeOffline($query, $offlineType)
    {
        switch (Str::lower($offlineType)) {
            case 'danger': // 31-60 d
                $ranges = $this->getDateRange('previous30Days');
                break;

            case 'warning': // 15-30 d
                $ranges = $this->getDateRange('previous15Days');
                break;

            default: // info | 7-14 d
                $ranges = $this->getDateRange('previous7Days');
                break;
        }

        return $query->whereBetween(
            DB::raw('DATE(updated_at)'), $this->convertRangesToStringFormat($ranges)
        );
    }

    // Report
    public function getDashboardsTags()
    {
        return [
            'totalKey' => $this->getTotalKey(),
            'totalKeyTwoYear' => $this->totalKeyOverTwoYear(),
            'totalKeyOneYear' => $this->totalKeyOneYear(),
            'totalKeySixMonth' => $this->totalKeySixMonth(),
            'totalKeyThreeMonth' => $this->totalKeyThreeMonth(),
            'totalKeyOneMonth' => $this->totalKeyOneMonth(),
            'totalKeyTrial' => $this->totalKeyTrial(),
        ];
    }
    protected function getTotalKey()
    {
        return $this->notTrial()->count('id');
    }
    public function totalKeyOverTwoYear()
    {
        return $this->where('invoice_period','>=','730')->notTrial()->count('id');
    }
    public function totalKeyOneYear()
    {
        return $this->where('invoice_period','365')->notTrial()->count('id');
    }
    public function totalKeySixMonth()
    {
        return $this->where('invoice_period','180')->notTrial()->count('id');
    }
    public function totalKeyThreeMonth()
    {
        return $this->where('invoice_period','90')->notTrial()->count('id');
    }
    public function totalKeyOneMonth()
    {
        return $this->where('invoice_period','30')->notTrial()->count('id');
    }
    protected function totalKeyTrial()
    {
        return $this->query()->trial()->count('id');
    }

    // Login
    public function loginTool(string $token) : PlanSubscription
    {
        $this->attributes['token'] = $token;
        $this->attributes['online'] = true;
        return $this;
    }
    public function logoutTool() : PlanSubscription
    {
        $this->attributes['online'] = false;
        return $this;
    }
    public function scopeStartBetween($query, string $date)
    {
        // YYYY-MM-DD,YYY-MM-DD
        [$from, $to] = explode(';', $date);
        $ranges = [
            Carbon::createFromFormat('Y-m-d', $from),
            Carbon::createFromFormat('Y-m-d', $to),
        ];
        return $query->whereBetween(
            DB::raw('DATE(starts_at)'), $this->convertRangesToStringFormat($ranges)
        );
    }

    protected static function booted()
    {
        static::addGlobalScope(new PlanSubscriptionUserScope);
        static::addGlobalScope(new LatestScope);
    }

}
