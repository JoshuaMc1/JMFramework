<?php

namespace App\Console\Commands;

require_once __DIR__ . '/../../../lib/Global/Global.php';

use Illuminate\Console\Command;

class CreateStorageLinkCommand extends Command
{
    protected $signature = 'storage:link';

    protected $description = 'Create a public storage link.';

    public function handle()
    {
        try {
            $publicPath = public_path();
            $storagePath = base_path() . '/storage/public';

            $this->comment('Creating storage link...');

            if (file_exists($publicPath . '/storage')) {
                $this->showErrorMessage('- The storage directory already exists.');

                return;
            }

            $this->createSymbolicLink($storagePath, $publicPath . '/storage') ?
                $this->showSuccessMessage('- The storage directory has been linked successfully.') :
                $this->showErrorMessage('- Failed to create the storage link.');
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

    protected function createSymbolicLink($target, $link)
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ?
            $this->createWindowsSymbolicLink($target, $link) :
            $this->createUnixSymbolicLink($target, $link);
    }

    protected function createWindowsSymbolicLink($target, $link)
    {
        exec("mklink /J \"$link\" \"$target\"", $output, $returnVar);
        return $returnVar === 0;
    }

    protected function createUnixSymbolicLink($target, $link)
    {
        return symlink($target, $link);
    }
}
