<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateControllerCommand extends Command
{
    protected $signature = 'create:controller {name} {--r}';

    protected $description = 'Create a new controller';

    public function handle()
    {
        try {
            $name = $this->argument('name');
            $filename = $this->getControllerFilePath($name);

            $resource = $this->option('r');

            $directory = $this->getDirectoryFromFilePath($filename);
            $this->createDirectory($directory);

            if (file_exists($filename)) {
                $this->error('The controller already exists!');
            } else {
                $stub = $resource ? $this->getResourceControllerStub($name) : $this->getControllerStub($name);
                file_put_contents($filename, $stub);
                $this->info('Controller successfully created.');
            }
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
    }

    protected function getControllerFilePath($name)
    {
        $controllerPath = $this->getControllerPath($name);
        $filename = dirname(__DIR__, 3) . "/app/Controllers/{$controllerPath}.php";
        return $filename;
    }

    protected function getDirectoryFromFilePath($filename)
    {
        return dirname($filename);
    }

    protected function createDirectory($directory)
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    protected function getControllerPath($name)
    {
        $name = str_replace('\\', '/', $name);
        $name = ltrim($name, '/');

        return $name;
    }

    private function getNamespace($name)
    {
        $parts = explode('/', $name);
        $directory = count($parts) > 1 ? '\\' . $parts[0] : '';
        return $directory;
    }

    private function getClassName($name)
    {
        $parts = explode('/', $name);
        return ucfirst(end($parts));
    }

    protected function getControllerStub($name)
    {
        $namespace = $this->getNamespace($name);
        $className = $this->getClassName($name);

        return <<<EOD
<?php

namespace App\Controllers{$namespace};

class $className
{
    //
}
EOD;
    }

    protected function getResourceControllerStub($name)
    {
        $namespace = $this->getNamespace($name);
        $className = $this->getClassName($name);

        return <<<EOD
<?php

namespace App\Controllers{$namespace};

use Lib\Http\Request;

class $className
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request \$request)
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

    public function update(Request \$request, \$id)
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
