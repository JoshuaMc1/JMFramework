<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateModel extends Command
{
    protected $signature = 'model {name}';

    protected $description = 'Crear un nuevo modelo';

    public function handle()
    {
        $name = $this->argument('name');
        $filename = dirname(__DIR__, 3) . "/app/Models/{$name}.php";
        $stub = <<<EOD
        <?php

        namespace App\Models;

        class $name extends Model
        {
            protected \$table = '$name';
        }
        EOD;

        if (file_exists($filename)) {
            $this->error('El modelo ya existe!');
        } else {
            file_put_contents($filename, $stub);
            $this->info('Modelo creado correctamente.');
        }
    }
}
