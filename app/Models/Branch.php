<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use App\Traits\SetDateRangesQuery;
use App\Helpers\Traits\DateRangeHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory, SoftDeletes, SetDateRangesQuery, DateRangeHelper;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function getRules()
    {
        $model = request()->branch;
        return [
            'name' => ['sometimes', 'required', 'string'],
            'slug' => ['sometimes', 'required', 'string',
                Rule::unique('branches', 'slug')->where(function ($query) use ($model) {
                    return $query->where('id', '!=', $model?->id);
                }),
            ],
        ];
    }

    public function getRevenueAttribute()
    {
        return $this->invoices()->sumBetweenRanges($this->ranges);
    }

    public function getRevenueTodayAttribute()
    {
        return $this->invoices()->getTotalRevenueOf('today');
    }

    public function getRevenueApproveAttribute()
    {
        return $this->invoices()->paided()->sumBetweenRanges($this->ranges);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
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
