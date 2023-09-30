<?php

namespace App\Console\Commands;

use App\Models\Auction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AuctionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auction:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command checks expiry';

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
        $auctions = Auction::whereNull('expired_at')->get();
        $now = Carbon::now();
        foreach ($auctions as $auction){
            if($auction->ending_at <= $now){
                $auction->expired_at = $now;

                $auction->save();
                dump($auction->expired_at);
                $checkBid = $auction->bids()->where('is_selected',1)->count();
                dump($checkBid);
                if($checkBid == 0){
                    $highestBid = $auction->bids()->orderBy('amount','desc')->first();
                    dump($highestBid);
                    if($highestBid != null){
                        $highestBid->is_selected = 1;
                        $highestBid->save();
                    }

                }
            }

        }
    }
}
