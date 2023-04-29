<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateCommand extends Command
{
    protected $signature = 'command {name}';

    protected $description = 'Crear un nuevo comando';

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
            $this->error('El comando ya existe!');
        } else {
            file_put_contents($filename, $stub);
            $this->info('Comando se a creado correctamente.');
        }
    }
}
