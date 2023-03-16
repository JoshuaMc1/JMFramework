<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HelloWorldCommand extends Command
{
    protected $signature = 'hello';

    protected $description = 'Say hello world';

    public function handle()
    {
        $this->info('Hello World!');
    }
}
