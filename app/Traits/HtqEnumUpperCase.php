<?php
declare(strict_types=1);

namespace App\Traits;

use Closure;
use Illuminate\Support\Str;

trait HtqEnumUpperCase
{
    protected static function values(): Closure
    {
        return function (string $name) {            
            return mb_strtoupper(Str::slug($name));
        };
    }
}
