<?php

namespace App\Console\Commands;

require_once __DIR__ . '/../../../lib/Global/Global.php';

use Illuminate\Console\Command;

class CreateMiddlewareCommand extends Command
{
    protected $signature = 'create:middleware {name}';

    protected $description = 'Create a new middleware';

    public function handle()
    {
        try {
            $name = $this->argument('name');
            $filename = middleware_path() . "/{$name}.php";

            $this->comment('Creating new middleware...');

            $this->middlewareExists($filename) ?
                $this->showErrorMessage('- The middleware already exists!') :
                $this->createNewCommand($filename, $name);
        } catch (\Throwable $th) {
            $this->showErrorMessage($th->getMessage());
        }
    }

    private function createNewCommand($filename, $name)
    {
        file_put_contents($filename, $this->getMiddlewareStub($name));

        $this->showSuccessMessage('- Command has been successfully created.');
    }

    private function showErrorMessage($message)
    {
        $this->line('');
        $this->error($message);
        $this->line('');
    }

    private function showSuccessMessage($message)
    {
        $this->line('');
        $this->info($message);
        $this->line('');
    }

    private function middlewareExists($filename)
    {
        return file_exists($filename);
    }

    public function getMiddlewareStub($name)
    {
        return addcslashes(
            <<<EOD
            <?php

            namespace App\Http\Middleware;

            use Lib\Http\Middleware\Contracts\MiddlewareInterface;
            use Lib\Http\Request;

            class $name implements MiddlewareInterface
            {
                public function handle(callable \$next, Request \$request)
                {
                    return \$next();
                }
            }
            EOD,
            "\v"
        );
    }
}
