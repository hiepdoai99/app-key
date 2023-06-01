<?php

namespace App\Actions;

use App\Models\Kpi;
use App\Models\User;
use Spatie\QueueableAction\QueueableAction;

class KpiStoreAction
{
    use QueueableAction;

    /**
     * Create a new action instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Prepare the action for execution, leveraging constructor injection.
    }

    /**
     * Execute the action.
     *
     * @return mixed
     */
    public function execute(array $kpiData)
    {
        if (!empty($kpiData['user_id'])) {
            $user = User::find($kpiData['user_id']);
            $kpiData['team_id'] = $user->team?->id;
            $kpiData['branch_id'] = $user->branch?->id;
        }

        return Kpi::create($kpiData);
    }
}
