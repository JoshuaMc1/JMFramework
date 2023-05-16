<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TailwindCommand extends Command
{
    protected $signature = 'tailwind:generate';

    protected $description = 'Compile Tailwind CSS style sheet';

    public function handle()
    {
        $this->info('Generating Tailwind CSS...');
        exec('npx tailwindcss -i resources/css/app.css -o public/resources/css/app.css --watch');
        $this->info('Tailwind CSS generated successfully!');
    }
}
