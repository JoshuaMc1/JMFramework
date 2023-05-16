<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Lib\Support\Cache;

class CacheExpiredClearCommand extends Command
{
    protected $signature = 'cache:clear';

    protected $description = 'Clear already expired cache.';

    public function handle()
    {
        $result = Cache::clear();

        if ($result) {
            $this->info('The cache has been successfully cleared.');
        } else {
            $this->error('An error occurred while clearing the cache!');
        }
    }
}
