<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateController extends Command
{
    protected $signature = 'controller {name}';

    protected $description = 'Crear un nuevo controlador';

    public function handle()
    {
        $name = $this->argument('name');
        $filename = dirname(__DIR__, 3) . "/app/Controllers/{$name}.php";
        $stub = <<<EOD
        <?php

        namespace App\Controllers;

        class $name extends Controller
        {
            //
        }
        EOD;

        if (file_exists($filename)) {
            $this->error('El controlador ya existe!');
        } else {
            file_put_contents($filename, $stub);
            $this->info('Controlador creado correctamente.');
        }
    }
}
