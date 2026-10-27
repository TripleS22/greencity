<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixHistoryDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-history-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing inventory history dates...');
        
        $affected = \App\Models\InventoryHistory::whereNull('transaction_date')
            ->update([
                'transaction_date' => \Illuminate\Support\Facades\DB::raw('DATE(created_at)')
            ]);
            
        $this->info("Updated {$affected} records.");
    }
}
