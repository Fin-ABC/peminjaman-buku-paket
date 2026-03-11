<?php

namespace App\Console\Commands;

use App\Models\TransactionDetail;
use Illuminate\Console\Command;

class checkOverdueTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Untuk mengecek dan mengupdate keterlambatan pengembalian buku';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updated = TransactionDetail::where('status', 'Borrowed')
            ->whereDate('return_date', '<', now())
            ->update(['status' => 'Overdue']);

        $this->info("Updated {$updated} transaction details to Overdue status.");

        return 0;
    }
}
