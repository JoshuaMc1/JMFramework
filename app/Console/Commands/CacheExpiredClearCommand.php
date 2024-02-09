<?php

namespace App\Console\Commands;

require_once __DIR__ . '/../../../lib/Global/Global.php';

use Illuminate\Console\Command;
use Lib\Support\Cache\Cache;

class CacheExpiredClearCommand extends Command
{
    protected $signature = 'cache:clear';

    protected $description = 'Clear already expired cache.';

    public function handle()
    {
        $result = Cache::clear();

        (!$result) ?
            $this->error('An error occurred while clearing the cache!') :
            $this->info('The cache has been successfully cleared.');
    }
}
