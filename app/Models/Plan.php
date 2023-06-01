<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Bpuig\Subby\Models\Plan as SubbyPlan;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends SubbyPlan
{
    use HasFactory;

    public function getFillable()
    {
        $fillable = parent::getFillable();
        $fillable = array_merge(
            $fillable,
            ['product_id']
        );
        return $fillable;
    }

    public function getRules() : array
    {
        $rules = parent::getRules();
        $model = request()->plan;
        $rules = array_merge(
            $rules,
            [
                'tag' => ['sometimes','required','max:150',
                    Rule::unique(config('subby.tables.plans'), 'tag')->where(function ($query) use ($model) {
                        return $query->where('id', '!=', $model?->id);
                    }),
                ],
                'product_id' => ['nullable', 'integer', 'min:1', 'exists:products,id'],
                'invoice_period' => ['sometimes', 'integer', 'min:1', 'max:1000000000'],
            ]
        );
        return $rules;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeThreeDayTrial($query)
    {
        return $query->where('trial_period', 3);
    }
}
