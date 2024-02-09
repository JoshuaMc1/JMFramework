<?php

namespace App\Console\Commands;

require_once __DIR__ . '/../../../lib/Global/Global.php';

use Illuminate\Console\Command;

class RunServerCommand extends Command
{
    protected $signature = 'server {--host=localhost} {--port=8000}';

    protected $description = 'Create local server.';

    public function handle()
    {
        $host = $this->option('host');
        $port = $this->option('port');
        $doc_root = public_path();
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
