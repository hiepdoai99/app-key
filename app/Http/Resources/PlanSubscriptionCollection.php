<?php

namespace App\Http\Resources;

use App\Traits\HtqPaginationResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PlanSubscriptionCollection extends ResourceCollection
{
    use HtqPaginationResource;
}
