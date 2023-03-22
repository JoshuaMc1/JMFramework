<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateController extends Command
{
    protected $signature = 'controller {name} {--r}';

    protected $description = 'Crear un nuevo controlador';

    public function handle()
    {
        $name = $this->argument('name');
        $filename = dirname(__DIR__, 3) . "/app/Controllers/{$name}.php";

        $resource = $this->option('r');

        if (file_exists($filename)) {
            $this->error('El controlador ya existe!');
        } else {
            $stub = $resource ? $this->getResourceControllerStub($name) : $this->getControllerStub($name);
            file_put_contents($filename, $stub);
            $this->info('Controlador creado correctamente.');
        }
    }

    protected function getControllerStub($name)
    {
        return <<<EOD
        <?php

        namespace App\Controllers;

        class $name extends Controller
        {
            //
        }
        EOD;
    }

    protected function getResourceControllerStub($name)
    {
        return <<<EOD
        <?php

        namespace App\Controllers;

        class $name extends Controller
        {
            public function index()
            {
                //
            }

            public function create()
            {
                //
            }

            public function store(\$request)
            {
                //
            }

            public function show(\$id)
            {
                //
            }

            public function edit(\$id)
            {
                //
            }

            public function update(\$request, \$id)
            {
                //
            }

            public function destroy(\$id)
            {
                //
            }
        }
        EOD;
    }
}
