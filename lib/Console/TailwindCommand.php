<?php

namespace Lib\Console;

use Illuminate\Console\Command;

/**
 * Class TailwindCommand
 * 
 * This class generates the Tailwind CSS style sheet.
 * 
 * @author Joshua Mclean
 */
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
