<?php

namespace App\Models;

use App\Scopes\LatestScope;
use App\Scopes\ActivityUserScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    use HasFactory;

    public function getList()
    {
        return [

        ];
    }

    protected static function booted()
    {
        static::addGlobalScope(new ActivityUserScope);
        static::addGlobalScope(new LatestScope);
    }
}
