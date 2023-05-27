<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateStorageDirectoryCommand extends Command
{
    protected $signature = 'storage:directory';

    protected $description = 'Create a public storage directory.';

    public function handle()
    {
        $rootPath = dirname(__DIR__, 3);

        $publicPath = $rootPath . '/public';

        $directoryPath = $publicPath . '/storage';

        if (is_dir($directoryPath)) {
            $this->error('The folder already exists.');
            return;
        }

        if (mkdir($directoryPath, 0666)) {
            $gitignore = fopen($directoryPath . '/.gitignore', 'w');
            fwrite($gitignore, $this->getStub());
            $this->info('The folder has been successfully created.');
        } else {
            $this->error('An error occurred while creating the folder.');
        }
    }

    protected function getStub()
    {
        $stub = <<<EOD
        *
        !.gitignore
        EOD;

        return $stub;
    }
}
