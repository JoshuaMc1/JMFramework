<?php

namespace App\Console\Commands;

require_once __DIR__ . '/../../../lib/Global/Global.php';

use Illuminate\Console\Command;
use Lib\Support\File;

class ClearViewCommand extends Command
{
    protected $signature = 'view:clear';

    protected $description = 'This command will clear all views';

    public function handle()
    {
        try {
            $this->clearViews();
        } catch (\Throwable $th) {
            $this->showErrorMessage($th->getMessage());
        }
    }

    private function clearViews()
    {
        $path = cache_path() . '/views';
        $this->comment('Clearing views...');

        if (is_dir($path)) {
            $this->deleteFilesAndDirectories($path);
        }

        $this->showSuccessMessage('- Views cleared');
    }

    private function deleteFilesAndDirectories($path)
    {
        $files = File::scandir($path);

        foreach ($files as $file) {
            $filePath = $path . '/' . $file;

            if (is_file($filePath) && substr($file, -4) === '.php') {
                File::delete($filePath);
            }

            if (is_dir($filePath)) {
                File::deleteDirectory($filePath);
            }
        }
    }

    private function showSuccessMessage($message)
    {
        $this->line('');
        $this->info($message);
        $this->line('');
    }

    private function showErrorMessage($message)
    {
        $this->line('');
        $this->error($message);
        $this->line('');
    }
}
