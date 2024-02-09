<?php

namespace App\Console\Commands;

require_once __DIR__ . '/../../../lib/Global/Global.php';

use Illuminate\Console\Command;

class CreateCommand extends Command
{
    protected $signature = 'create:command {name}';

    protected $description = 'Create a new command';

    public function handle()
    {
        $name = $this->argument('name');

        $filename = app_path() . "/Console/Commands/{$name}.php";

        $stub = <<<EOD
            <?php

            namespace App\Console\Commands;

            use Illuminate\Console\Command;

            class $name extends Command
            {
                protected \$signature = '';

                protected \$description = '';

                public function handle()
                {
                    // 
                }
            }
            EOD;

        if (file_exists($filename)) {
            $this->error('The command already exists!');
        } else {
            file_put_contents($filename, $stub);
            $this->info('Command has been successfully created.');
        }
    }
}
