<?php
declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Schema;

trait HtqScopeExclude
{
    public function scopeExclude($query, $value = [])
    {
        return $query->select(array_diff(
            Schema::getColumnListing($this->getTable()),
            (array) $value
        ));
    }
}
