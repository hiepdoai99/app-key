<?php

namespace App\Enums;

use App\Traits\HtqEnumUpperCase;
use Spatie\Enum\Laravel\Enum;

/**
 * @method static self member()
 * @method static self customer()
 * @method static self walk_in()
 * @method static self guest()
 */
final class UserTypesEnum extends Enum
{
    use HtqEnumUpperCase;
}
