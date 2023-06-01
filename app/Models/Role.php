<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function getRules() : array
    {
        $model = request()->role;
        return [
            'permissions' => ['nullable', 'array'],
            'name' => ['nullable',
                Rule::unique($this->getTable(), 'name')->where(function ($query) use($model) {
                    return $query->where('id', '!=', $model?->id);
                }),
            ]
        ];
    }
}
