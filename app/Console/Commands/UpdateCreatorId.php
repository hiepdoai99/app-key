<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class UpdateCreatorId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:creator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     parent::__construct();
    // }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $invoices = Invoice::with('user')->get();

        $invoices->map(function($invoice){
            $subscriber = $invoice->subscriber()->first();
            if (is_null($subscriber->creator_id)) {
                $subscriber->fill(['creator_id' => $invoice->user->id])->save();
            }
        });
        $this->info('Done!');
        return 0;
    }
}
