<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SendQueuedEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send';

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
        $sentToday = Cache::get('emails_sent_today', 0);
        
        if ($sentToday >= 1000) {
            $this->info("Daily limit of 1000 emails reached.");
            return;
        }

        $customers = User::whereNull('email_sent_at')
            ->limit(10) // Process 10 customers per hour
            ->get();

        if ($customers->isEmpty()) {
            $this->info("No customers left to email.");
            return;
        }

        foreach ($customers as $customer) {
            SendEmailJob::dispatch($customer);

            // Mark as sent to avoid duplicate processing
            $customer->update(['email_sent_at' => now()]);
        }

        Cache::increment('emails_sent_today', $customers->count());

        $this->info("Sent {$customers->count()} emails.");
    }
}
