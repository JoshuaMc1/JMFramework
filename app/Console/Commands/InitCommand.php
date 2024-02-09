<?php

namespace App\Console\Commands;

require_once __DIR__ . '/../../../lib/Global/Global.php';

use Illuminate\Console\Command;
use Lib\Support\Env;

class InitCommand extends Command
{
    protected $signature = 'init';

    protected $description = 'This command publishes the initial tables for the application.';

    public function handle()
    {
        Env::load();

        $directorySchemas = lib_path() . '/Database/Schemas/';
        $directoryDatabase = database_path();

        $this->info("Publishing initial tables...\n");

        $count = $this->copySchemasToDatabase($directorySchemas, $directoryDatabase);

        $this->info("[{$count}] tables created and published successfully.\n");

        $this->call('schema:run');
    }

    private function copySchemasToDatabase($source, $destination)
    {
        $files = array_diff(scandir($source), ['.', '..']);

        foreach ($files as $file) {
            copy("$source/$file", "$destination/$file");
        }

        return count($files);
    }
}
