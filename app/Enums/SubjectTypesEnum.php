<?php

namespace App\Enums;

use Closure;
use Illuminate\Support\Str;
use Spatie\Enum\Laravel\Enum;

/**
 * @method static self user()
 * @method static self invoice()
 */
final class SubjectTypesEnum extends Enum
{

    protected static function values(): Closure
    {
        return function (string $name) {
            return [
                'subject' => 'App\Models\\'.Str::ucfirst($name),
                'name' => 'Name\Models\\'.Str::ucfirst($name),
            ];
        };
    }
}
