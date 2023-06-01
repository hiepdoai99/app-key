<?php

namespace App\Enums;

use App\Traits\HtqEnumUpperCase;
use Spatie\Enum\Laravel\Enum;

/**
 * @method static self user_create()
 * @method static self user_read()
 * @method static self user_update()
 * @method static self user_delete()
 *
 * @method static self customer_create()
 * @method static self customer_read()
 * @method static self customer_update()
 * @method static self customer_delete()
 *
 * @method static self role_create()
 * @method static self role_read()
 * @method static self role_update()
 * @method static self role_delete()
 *
 * @method static self permission_create()
 * @method static self permission_read()
 * @method static self permission_update()
 * @method static self permission_delete()
 *
 * @method static self plan_create()
 * @method static self plan_read()
 * @method static self plan_update()
 * @method static self plan_delete()
 *
 * @method static self planfeature_create()
 * @method static self planfeature_read()
 * @method static self planfeature_update()
 * @method static self planfeature_delete()
 *
 * @method static self plansubscription_create()
 * @method static self plansubscription_read()
 * @method static self plansubscription_update()
 * @method static self plansubscription_delete()
 *
 * @method static self product_create()
 * @method static self product_read()
 * @method static self product_update()
 * @method static self product_delete()
 *
 * @method static self invoice_create()
 * @method static self invoice_read()
 * @method static self invoice_update()
 * @method static self invoice_delete()
 *
 * @method static self team_create()
 * @method static self team_read()
 * @method static self team_update()
 * @method static self team_delete()
 *
 * @method static self branch_create()
 * @method static self branch_read()
 * @method static self branch_update()
 * @method static self branch_delete()
 *
 * @method static self kpi_create()
 * @method static self kpi_read()
 * @method static self kpi_update()
 * @method static self kpi_delete()
 *
 * @method static self bank_create()
 * @method static self bank_read()
 * @method static self bank_update()
 * @method static self bank_delete()
 *
 * @method static self activity_create()
 * @method static self activity_read()
 * @method static self activity_update()
 * @method static self activity_delete()
 *
 */

final class PermissionsEnum extends Enum
{
    use HtqEnumUpperCase;
}
