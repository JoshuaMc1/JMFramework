<?php

namespace Lib\Console;

require_once __DIR__ . '/../Global/Global.php';

use Illuminate\Console\Command;

/**
 * Class CreateControllerCommand
 * 
 * This command will create a controller.
 * 
 * @author Joshua Mclean
 */
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

            $this->comment('Creating controller...');

            file_exists($filename)
                ? $this->showErrorMessage()
                : (
                    file_put_contents($filename, $stub = $resource ? $this->getResourceControllerStub($name) : $this->getControllerStub($name))
                    && $this->showSuccessMessage()
                );
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
    }

    protected function showErrorMessage()
    {
        $this->showMessage('error', '- The controller already exists!');
    }

    protected function showSuccessMessage()
    {
        $this->showMessage('info', '- Controller successfully created.');
    }

    protected function showMessage($type, $message)
    {
        $this->line('');
        $this->{$type}($message);
        $this->line('');
    }

    protected function getControllerFilePath($name)
    {
        return controller_path() . "/{$this->getControllerPath($name)}.php";
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
        return ltrim(str_replace('\\', '/', $name), '/');
    }

    private function getNamespaceAndClassName($name)
    {
        $parts = explode('/', $name);
        $className = ucfirst(array_pop($parts));
        $namespace = count($parts) > 0 ? implode('\\', $parts) : '';

        return [$namespace, $className];
    }

    protected function getControllerStub($name)
    {
        list($namespace, $className) = $this->getNamespaceAndClassName($name);

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
        list($namespace, $className) = $this->getNamespaceAndClassName($name);

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
