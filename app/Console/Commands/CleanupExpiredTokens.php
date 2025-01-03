<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Token;
use Carbon\Carbon;

class CleanupExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tokens:cleanup';

    /**
     * The console command description.
     */
    protected $description = 'Delete expired API tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $deleted = Token::where('expires_at', '<', $now)->delete();

        $this->info("Deleted {$deleted} expired tokens.");
    }
}
