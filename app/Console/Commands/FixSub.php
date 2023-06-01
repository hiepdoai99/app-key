<?php

namespace App\Console\Commands;

use App\Models\PlanSubscription;
use Illuminate\Console\Command;

class FixSub extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:sub';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Subscriptions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $subs = PlanSubscription::withoutGlobalScopes()->get();
        $subs->each(function($sub) {
            $sub->tag = $sub->license;
            $sub->save();
        });
        return 0;
    }
}
