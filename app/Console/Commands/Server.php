<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Server extends Command
{
    protected $signature = 'server';

    protected $description = 'Crear servidor local';

    public function handle()
    {
        $host = 'localhost';
        $port = 8000;
        $doc_root = dirname(__DIR__, 3) . '/public';
        $command = "php -S $host:$port -t $doc_root";

        $result = exec($command);

        if ($result) {
            $this->info("Server running at http://$host:$port");
        } else {
            $this->error("Server not running");
        }
    }
}
