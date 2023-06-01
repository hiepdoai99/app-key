<?php

namespace App\Models;

use App\Enums\RolesEnum;
use App\Helpers\Traits\DateRangeHelper;
use App\Scopes\LeaderScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kpi extends Model
{
    use HasFactory, SoftDeletes;
    use DateRangeHelper;

    protected $fillable = [
        'name',
        'code',
        'target',
        'start_at',
        'end_at',
        'branch_id',
        'team_id',
        'user_id',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function getRules()
    {
        $model = request()->kpi;
        return [
            'name' => ['sometimes', 'required', 'string'],
            'code' => ['sometimes', 'required', 'string',
                Rule::unique('kpis', 'code')->where(function ($query) use($model) {
                    return $query->where('id', '!=', $model?->id);
                })
            ],
            'target' => ['nullable', 'integer', 'min:0'],
            'start_at' => ['sometimes', 'required', 'date_format:Y-m-d H:i:s'],
            'end_at' => ['sometimes', 'required', 'date_format:Y-m-d H:i:s'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'user_id' => ['nullable', 'exists:users,id'],
        ];
    }

    public function setCodeAttribute($value)
    {
        $start_at = (explode(' ',request()->start_at))[0];
        $end_at = (explode(' ',request()->end_at))[0];
        if ('1' === $value || 1 ===$value) {
            $this->attributes['code'] = request()->user_id.'-'.$start_at.'_'.$end_at;
        }
    }
    public function scopeSales($query)
    {
        $id = Auth::user()->id;
        return $query->where('user_id',$id );
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected static function booted()
    {
        if (Auth::hasUser()) {
            $user = Auth::user();
            if ($user->hasRole((string)RolesEnum::sales())) {
                static::addGlobalScope('sales', function($query) use($user){
                    return $query->where('user_id', $user->id);
                });
            }

        }
        static::addGlobalScope(new LeaderScope);
    }

    public function scopeTotalKpi($query): int
    {
        return (int)$query->sum('target');
    }
    public function scopeSumBetweenRanges($query, array $ranges)
    {
        $table = $this->getTable();
        return $query->whereBetween(
            DB::raw("DATE($table.start_at)"), $this->convertRangesToStringFormat($ranges)
        )->totalKpi();
    }
    public function scopeGetTotal($query, string $within = null, $year = 0)
    {
        $ranges = $this->getStartAndEndOf($within, $year);
        $sum = $query->sumBetweenRanges($ranges);
        if ( Auth::user()->hasRoleAdmin() &&  $sum === 0){
            $sales = User::whereHas('roles' , function($q){
                $q->where('name', (string)RolesEnum::sales());
            })->count('id');
            return $sales * 10000000;
        }

        return $sum;
    }


}
