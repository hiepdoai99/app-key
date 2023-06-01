<?php

namespace App\Scopes;

use App\Enums\RolesEnum;
use App\Scopes\InvoiceUserScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class MemberUserScope implements Scope
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
        if (Auth::hasUser() && !Auth::user()->hasRoleAdmin()) {
            $builder->whereHas('memberInvoices', function (Builder $query) {
                new InvoiceUserScope;
            });
        }
    }

}
