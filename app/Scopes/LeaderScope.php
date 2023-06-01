<?php

namespace App\Scopes;

use App\Enums\RolesEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class LeaderScope implements Scope
{

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::hasUser() && Auth::user()->hasRole((string)RolesEnum::leader())) {
            $builder->whereHas('team', function($query){
                $query->where('id', Auth::user()->team->id);
            });
        }
    }

}
