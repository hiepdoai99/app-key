<?php

namespace App\Models;

use App\Enums\RolesEnum;
use App\Scopes\LatestScope;
use App\Scopes\LeaderScope;
use App\Traits\HtqGetNextId;
use Illuminate\Support\Carbon;
use App\Traits\HtqScopeExclude;
use Illuminate\Validation\Rule;
use App\Enums\InvoiceStatusEnum;
use App\Scopes\InvoiceUserScope;
use App\Traits\SpatieLogsActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Traits\DateRangeHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, HtqGetNextId, HtqScopeExclude, SpatieLogsActivity;
    use DateRangeHelper;

    protected $fillable = [
        'user_id',
        'subscriber_id',
        'plan_id',
        'product_id',
        'plan_subscription_id',
        'total',
        'code',
        'status',
        'tax',
        'bank_id',
        'bank_memo',
        'transaction',
        'coupon',
        'discount',
        'his',
        'note',
        'reason',
    ];

    protected $casts = [
        'status' => InvoiceStatusEnum::class,
        'total' => 'float',
    ];

    public function getRules() : array
    {
        $model = request()->invoice;
        return [
            'user_id' => ['sometimes', 'required', 'integer', 'min:1', 'exists:users,id'],
            'subscriber_id' => ['sometimes', 'required', 'integer', 'min:1', 'exists:users,id'],
            'product_id' => ['sometimes', 'required', 'integer', 'min:1', 'exists:products,id'],
            'plan_id' => ['sometimes', 'required', 'integer', 'min:1', 'exists:'.config('subby.tables.plans').',id'],
            'plan_subscription_id' => ['nullable', 'integer', 'min:1', 'exists:'.config('subby.tables.plan_subscriptions').',id'],
            'total' => ['sometimes', 'required', 'numeric', 'min:0'],
            'code' => ['sometimes', 'required', 'string',
                Rule::unique('invoices', 'code')->where(function ($query) use($model) {
                    return $query->where('id', '!=', $model?->id);
                }),
            ],
            'status' => ['sometimes', 'required', 'enum:'.InvoiceStatusEnum::class],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'bank_id' => ['nullable', 'min:0', 'exists:banks,id'],
            'bank_memo' => ['nullable', 'string'],
            'transaction' => ['nullable', 'string'],
            'coupon' => ['nullable', 'string'],
            'discount' => ['nullable', 'numeric'],
            'his' => ['sometimes', 'required', 'string',
                Rule::unique('invoices', 'his')->where(function ($query) use($model) {
                    return $query->where('product_id', $model?->product_id)
                        ->where('id', '!=', $model?->id);
                }),
            ],
            'note' => ['nullable'],
            'reason' => ['nullable'],
            'upload_invoice' => ['nullable', 'mimes:png,jpg,jpeg|max:2048'],
        ];
    }

    public function setCodeAttribute($value)
    {
        if ('1' == $value) {
            $value = $this->getNextId();
        }
        $this->attributes['code'] = 'INV_1200' . $value;
    }

    public function isUpgrade(int $tier) : bool
    {
        return $tier > $this->tier ? true : false;
    }

    public function isDowngrade(int $tier) : bool
    {
        return $tier < $this->tier ? true : false;
    }

    public function team()
    {
        return $this->user->team;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subscriber_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function planSubscription(): BelongsTo
    {
        return $this->belongsTo(PlanSubscription::class, 'plan_subscription_id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function isRunning() : bool
    {
        return $this->planSubscription()->notTrial()->active()->count() === 1;
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
            DB::raw('DATE(created_at)'), $this->convertRangesToStringFormat($ranges)
        );
    }

    public function scopeOfTeam($query, $team_id)
    {
        return $query->whereHas('user', function($builder) use ($team_id){
            $builder->where('users.team_id', $team_id);
        });
    }

    public function scopeWhereEmail($query, $email)
    {
        $user = User::where('email',$email)->first();
        $table = $this->getTable();
        return $query->withInvoice()->whereIn('invoices.user_id', [$user->id])
            ->orWhereIn($table.'.subscriber_id',[$user->id]);
    }

    public function scopePaided($query)
    {
        $table = $this->getTable();
        return $query->where("$table.status", (string)InvoiceStatusEnum::paid());
    }

    public function scopeGetTotalRevenueOf($query, string $within = null, $year = 0)
    {
        if (0 === $year) {
            $year = nowFromApp()->year;
        }
        $ranges = $this->getStartAndEndOf($within, $year);

        return $query->sumBetweenRanges($ranges);
    }

    public function scopeSumBetweenRanges($query, array $ranges)
    {
        $table = $this->getTable();
        return $query->whereBetween(
            DB::raw("DATE($table.created_at)"), $this->convertRangesToStringFormat($ranges)
        )->getSum();
    }

    public function scopeGetSum($query)
    {
        return $query->sum('total');
    }

    public function getSumEveryDayOfMonth(string $month, string $year, bool $paided = false)
    {
        $query = $this->select(DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') AS sum_date"), DB::raw("SUM(total) AS sum_total"))
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year);
        if($paided){
            $query->paided();
        }
        return $query->orderBy('created_at')
            ->groupBy(DB::raw("(DATE_FORMAT(created_at, '%d-%m-%Y'))"))
            ->get();
    }

    protected static function booted()
    {
        if (Auth::hasUser() && Auth::user()->hasRole((string)RolesEnum::leader())) {
            static::addGlobalScope('leader', function($builder){
                $builder->ofTeam(Auth::user()->team_id);
            });
        }
        static::addGlobalScope(new InvoiceUserScope);
        static::addGlobalScope(new LatestScope);
    }
}
