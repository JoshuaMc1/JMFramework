<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TailwindCommand extends Command
{
    protected $signature = 'tailwind:generate';

    protected $description = 'Compile Tailwind CSS style sheet';

    public function handle()
    {
        try {
            $this->comment('Generating Tailwind CSS...');

            exec('npx tailwindcss -i resources/css/app.css -o public/css/app.css --watch');

            $this->showSuccessMessage('- Tailwind CSS generated successfully!');
        } catch (\Throwable $th) {
            $this->showErrorMessage($th->getMessage());
        }
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
}
