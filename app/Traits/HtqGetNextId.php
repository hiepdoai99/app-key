<?php
declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HtqGetNextId
{
    protected function getNextId() : int
    {
        $id = DB::select("SHOW TABLE STATUS LIKE '{$this->getTable()}'");
        return isset($id[0]) ? $id[0]->Auto_increment : 0;
    }
}
