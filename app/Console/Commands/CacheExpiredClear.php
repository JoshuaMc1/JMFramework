<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Lib\Cache;

class CacheExpiredClear extends Command
{
    protected $signature = 'cache:clear';

    protected $description = 'Limpiar cache que ya a expirado.';

    public function handle()
    {
        $result = Cache::clear();

        if ($result) {
            $this->info('La cache se a limpiado correctamente.');
        } else {
            $this->error('A ocurrido un error al limpiar la cache!');
        }
    }
}
