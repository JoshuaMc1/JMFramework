<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunServerCommand extends Command
{
    protected $signature = 'server';

    protected $description = 'Create local server and running tailwind css compiler';

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

        $tailwind_command = 'npx tailwindcss -i resources/css/app.css -o public/resources/css/app.css --watch';
        $tailwind_process = popen($tailwind_command, "r");

        if ($tailwind_process === false) {
            $this->error("Failed to start Tailwind CSS process");
            return;
        }

        pclose($server_process);
        pclose($tailwind_process);
    }
}
