<?php

namespace App\Enums;

use App\Traits\HtqEnumUpperCase;
use Spatie\Enum\Laravel\Enum;

/**
 * @method static self paid()
 * @method static self unpaid()
 * @method static self due()
 * @method static self overdue()
 */
final class InvoiceStatusEnum extends Enum
{
    use HtqEnumUpperCase;
}
