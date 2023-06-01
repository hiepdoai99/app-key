<?php

namespace App\Includes;

use Spatie\QueryBuilder\Includes\IncludeInterface;
use Illuminate\Database\Eloquent\Builder;

class InvoiceInclude implements IncludeInterface
{

    public function __construct()
    {
        //
    }

    public function __invoke(Builder $query, string $table)
    {
        $query->withInvoice();
    }
}
