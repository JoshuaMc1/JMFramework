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
        try {
            $host = $this->option('host');
            $port = $this->option('port');
            $doc_root = public_path();

            $server_command = "php -S $host:$port -t $doc_root";

            $server_process = popen($server_command, "r");

            $this->comment("Starting server...");

            if ($server_process === false) {
                $this->showErrorMessage("- Failed to start server.");
                return;
            }

            $this->showSuccessMessage("- Server started successfully. Listening on http://$host:$port");

            pclose($server_process);
        } catch (\Throwable $th) {
            $this->showErrorMessage($th->getMessage());
        }
    }

    private function showErrorMessage($message)
    {
        $this->line('');
        $this->error($message);
        $this->line('');
    }

    private function showSuccessMessage($message)
    {
        $this->line('');
        $this->info($message);
        $this->line('');
    }
}
