<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunServerCommand extends Command
{
    protected $signature = 'server';

    protected $description = 'Create local server.';

    public function handle()
    {
        $host = 'localhost';
        $port = 8000;
        $doc_root = dirname(__DIR__, 3) . '/public';
        $server_command = "php -S $host:$port -t $doc_root";

        $server_process = popen($server_command, "r");
        if ($server_process === false) {
            $this->error("Failed to start server process");
            return;
        }

        $this->info("Server running at http://$host:$port");

        pclose($server_process);
    }
}
