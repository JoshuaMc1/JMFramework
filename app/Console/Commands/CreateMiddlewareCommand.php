<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateMiddlewareCommand extends Command
{
    protected $signature = 'create:middleware {name}';

    protected $description = 'Create a new middleware';

    public function handle()
    {
        $name = $this->argument('name');
        $filename = dirname(__DIR__, 3) . "/app/Middleware/{$name}.php";

        if (file_exists($filename)) {
            $this->error('The middleware already exists!');
        } else {
            file_put_contents($filename, $this->getMiddlewareStub($name));
            $this->info('Middleware successfully created.');
        }
    }

    public function getMiddlewareStub($name)
    {
        return addcslashes(<<<EOD
        <?php

        namespace App\Http\Middleware;

        use Lib\Http\Middleware\MiddlewareInterface;
        use Lib\Http\Request;

        class $name implements MiddlewareInterface
        {
            public function handle(callable \$next, Request \$request)
            {
                return \$next();
            }
        }
        EOD, "\v");
    }
}