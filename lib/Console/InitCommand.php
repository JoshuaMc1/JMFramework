<?php

namespace Lib\Console;

require_once __DIR__ . '/../Global/Global.php';

use Illuminate\Console\Command;
use Lib\Support\Env;

/**
 * Class InitCommand
 * 
 * This command publishes the initial tables for the application.
 * 
 * @author Joshua Mclean
 */
class InitCommand extends Command
{
    protected $signature = 'init';

    protected $description = 'This command publishes the initial tables for the application.';

    public function handle()
    {
        try {
            Env::load();

            $directorySchemas = lib_path() . '/Database/Schemas/';
            $directoryDatabase = database_path();

            $this->comment("Publishing initial tables...");

            $count = $this->copySchemasToDatabase($directorySchemas, $directoryDatabase);

            $this->showSuccessMessage("- [{$count}] tables created and published successfully.");

            $this->call('schema:run');
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

    private function copySchemasToDatabase($source, $destination)
    {
        $files = array_diff(scandir($source), ['.', '..']);

        foreach ($files as $file) {
            copy("$source/$file", "$destination/$file");
        }

        return count($files);
    }
}
