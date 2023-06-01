<?php

namespace App\Actions;

use App\Models\Invoice;
use Spatie\QueueableAction\QueueableAction;

class UserRegisterToolValidateAction
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
    public function execute(array $dataUser) : bool
    {
        $masp = $dataUser['masp'];
        return Invoice::where('his', $dataUser['his'])
            ->whereHas('product', function($query) use ($masp){
                return $query->where('prefix_key', $masp);
            })->exists();
    }
}
