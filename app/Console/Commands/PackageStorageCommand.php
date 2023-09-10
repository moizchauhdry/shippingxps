<?php

namespace App\Console\Commands;

use App\Models\Package;
use App\Models\User;
use App\Notifications\PackageStorageNotification;
use Illuminate\Console\Command;

class PackageStorageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:package-storage';

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
        $package = Package::with('customer')->orderBy('created_at', 'asc')->where('storage_days_exceeded', '>', 0)->where('auctioned', 0)->get();

        foreach ($package as $key => $package) {
            if ($package->storage_days_exceeded > 0) {
                $user = User::where('email', $package->customer->email)->first();
                $notification = new PackageStorageNotification($package);
                $user->notify($notification);
            }
        }

        dd('Success.');
    }
}
