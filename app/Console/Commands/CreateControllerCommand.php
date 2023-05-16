<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateControllerCommand extends Command
{
    protected $signature = 'create:controller {name} {--r}';

    protected $description = 'Create a new controller';

    public function handle()
    {
        $name = $this->argument('name');
        $filename = dirname(__DIR__, 3) . "/app/Controllers/{$name}.php";

        $resource = $this->option('r');

        if (file_exists($filename)) {
            $this->error('The controller already exists!');
        } else {
            $stub = $resource ? $this->getResourceControllerStub($name) : $this->getControllerStub($name);
            file_put_contents($filename, $stub);
            $this->info('Controller successfully created.');
        }
    }

    protected function getControllerStub($name)
    {
        return addcslashes(<<<EOD
        <?php

        namespace App\Controllers;

        use function Lib\Global\view;

        class $name
        {
            //
        }
        EOD, "\v");
    }

    protected function getResourceControllerStub($name)
    {
        return addcslashes(<<<EOD
        <?php

        namespace App\Controllers;

        use function Lib\Global\view;

        class $name
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
        EOD, "\v");
    }
}
