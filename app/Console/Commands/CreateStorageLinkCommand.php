<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateStorageLinkCommand extends Command
{
    protected $signature = 'storage:link';

    protected $description = 'Create a public storage link.';

    public function handle()
    {
        try {
            $rootPath = dirname(__DIR__, 3);
            $publicPath = $rootPath . '/public';
            $storagePath = $rootPath . '/storage/public';

            if (file_exists($publicPath . '/storage')) {
                $this->error('The "public/storage" directory already exists.');
                return;
            }

            if ($this->createSymbolicLink($storagePath, $publicPath . '/storage')) {
                $this->info('The storage directory has been linked successfully.');
            } else {
                $this->error('Failed to create the storage link.');
            }
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
    }

    protected function createSymbolicLink($target, $link)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return $this->createWindowsSymbolicLink($target, $link);
        } else {
            return $this->createUnixSymbolicLink($target, $link);
        }
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
