<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    public function getRules() : array
    {
        $model = request()->permission;
        return [
            'name' => ['required',
                Rule::unique($this->getTable(), 'name')->where(function ($query) use($model) {
                    return $query->where('id', '!=', $model?->id);
                }),
            ],
        ];
    }
}
