<?php
declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Carbon;

trait SetDateRangesQuery
{
    public function getRangesAttribute()
    {
        if (request()->has(['month', 'years'])) {
            return $this->setRanges((int)request()->month, (int)request()->years);
        }
        return $this->setRanges('thisMonth');
    }

    private function setRanges($month = 1, int $years = 0)
    {
        $ranges = $this->getStartAndEndOf($month, 0 === $years ? Carbon::now()->year : $years);
        return count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges;
    }
}
