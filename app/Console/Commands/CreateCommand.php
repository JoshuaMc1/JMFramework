<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateCommand extends Command
{
    protected $signature = 'create:command {name}';

    protected $description = 'Create a new command';

    public function handle()
    {
        $name = $this->argument('name');

        $filename = dirname(__DIR__, 3) . "/app/Console/Commands/{$name}.php";

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
