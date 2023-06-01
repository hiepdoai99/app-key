<?php

namespace App\Models;

use App\Helpers\Traits\DateRangeHelper;
use App\Scopes\LatestScope;
use App\Traits\SetDateRangesQuery;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes, DateRangeHelper, SetDateRangesQuery;

    protected $fillable = [
        'name',
        'slug',
        'prefix_key',
        'description',
        'status',
        'version',
    ];
    protected $appends = ['revenue'];


    public function getRules()
    {
        $model = request()->product;
        return [
            'name' => ['sometimes', 'required', 'string'],
            'slug' => ['sometimes', 'required', 'string',
                Rule::unique('products', 'slug')->where(function ($query) use($model) {
                    return $query->where('id', '!=', $model?->id);
                }),
            ],
            'prefix_key' => ['sometimes', 'required', 'string',
                Rule::unique('products', 'prefix_key')->where(function ($query) use($model) {
                    return $query->where('id', '!=', $model?->id);
                })
            ],
            'description' => ['nullable'],
            'status' => ['nullable', 'boolean'],
            'version' => ['nullable', 'string'],
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

    public function setPrefixKeyAttribute($value)
    {
        $this->attributes['prefix_key'] = (Str::endsWith($value, '-'))
            ? Str::upper($value)
            : Str::upper($value).'-' ;
    }

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    public function planSubscriptions()
    {
        return $this->hasMany(PlanSubscription::class);
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new LatestScope);
    }
}
