<?php

namespace App\Models;

use App\Enums\UserTypesEnum;
use App\Helpers\Traits\DateRangeHelper;
use App\Traits\SetDateRangesQuery;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory, SoftDeletes, SetDateRangesQuery, DateRangeHelper;

    protected $fillable = [
        'name',
        'parent_id',
        'slug',
        'branch_id',
        'status',
    ];

    public function getRules()
    {
        $model = request()->team;
        return [
            'name' => ['sometimes', 'required', 'string'],
            'branch_id' => ['sometimes', 'required', 'integer', 'min:0', 'exists:branches,id'],
            'parent_id' => ['nulalble', 'integer', 'min:0', 'exists:teams,id'],
            'status' => ['nulalble', 'integer', 'min:0'],
            'slug' => ['sometimes', 'required', 'string',
                Rule::unique('teams', 'slug')->where(function ($query) use ($model) {
                    return $query->where('branch_id', $model?->branch_id)->where('id', '!=', $model?->id);
                }),
            ],
        ];
    }

    public function getRevenueAttribute()
    {
        return $this->invoices()->sumBetweenRanges($this->ranges);
    }

    public function getRevenueApproveAttribute()
    {
        return $this->invoices()->paided()->sumBetweenRanges($this->ranges);
    }

    public function parentTeam()
    {
        return $this->belongsTo(Team::class, 'parent_id');
    }

    public function childTeams()
    {
        return $this->hasMany(Team::class, 'parent_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function users(){
        return $this->hasMany(User::class);
    }

    public function kpis()
    {
        return $this->hasMany(Kpi::class);
    }

    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, User::class);
    }
}
