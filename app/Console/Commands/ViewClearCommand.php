<?php

namespace App\Console\Commands;

require_once __DIR__ . '/../../../lib/Global/Global.php';

use Illuminate\Console\Command;
use Lib\Support\File;

class ViewClearCommand extends Command
{
    protected $signature = 'view:clear';

    protected $description = 'This command will clear all views';

    public function handle()
    {
        $path = cache_path() . '/views';

        if (is_dir($path)) {
            $files = File::scandir($path);

            foreach ($files as $file) {
                if (is_file($path . '/' . $file) && substr($file, -4) === '.php') {
                    File::delete($path . '/' . $file);
                }

                if (is_dir($path . '/' . $file)) {
                    File::deleteDirectory($path . '/' . $file);
                }
            }
        }

        $this->info('Views cleared');
        $this->line("");
    }
}
