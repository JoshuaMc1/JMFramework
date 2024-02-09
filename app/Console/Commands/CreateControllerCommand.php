<?php

namespace App\Console\Commands;

require_once __DIR__ . '/../../../lib/Global/Global.php';

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
        $filename = controller_path() . "/{$controllerPath}.php";

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

        array_pop($parts);

        return implode('\\', $parts);
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

        $namespaceString = $namespace ? "namespace App\Http\Controllers\\$namespace;" : 'namespace App\Http\Controllers;';

        return <<<EOD
    <?php
    
    $namespaceString
    
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

        $namespaceString = $namespace ? "namespace App\Http\Controllers\\$namespace;" : 'namespace App\Http\Controllers;';

        return <<<EOD
<?php

$namespaceString

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

    public function show(Request \$request, string \$id)
    {
        //
    }

    public function edit(Request \$request, string \$id)
    {
        //
    }

    public function update(Request \$request, string \$id)
    {
        //
    }

    public function destroy(Request \$request, string \$id)
    {
        //
    }
}
EOD;
    }
}
