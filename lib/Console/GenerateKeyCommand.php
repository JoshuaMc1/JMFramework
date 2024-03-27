<?php

namespace Lib\Console;

require_once __DIR__ . '/../Global/Global.php';

use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Class GenerateKeyCommand
 * 
 * This command will generate an application key.
 * 
 * @author Joshua Mclean
 */
class GenerateKeyCommand extends Command
{
    protected $signature = 'key:generate';

    protected $description = 'Generate an application key.';

    public function handle()
    {
        try {
            $envPath = $this->getEnvPath();
            $key = $this->generateKey();

            $this->comment('Generating new application key...');

            $this->updateEnvFile($envPath, $key) ?
                $this->showSuccessMessage('- Application key generated successfully and updated in .env file.') :
                $this->showErrorMessage('- Failed to update the application key in .env file.');
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

    /**
     * Get the path to the .env file.
     *
     * @return string
     */
    private function getEnvPath(): string
    {
        return base_path() . '/.env';
    }

    /**
     * Generate a new key
     * 
     * @return string
     */
    private function generateKey()
    {
        return Str::random(10) . '|' . Str::random(90);
    }

    private function updateEnvFile($envPath, $key)
    {
        $envContent = file_get_contents($envPath);

        if ($envContent === false) {
            return false;
        }

        $updatedContent = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $envContent);

        if ($updatedContent === null) {
            return false;
        }

        return file_put_contents($envPath, $updatedContent) !== false;
    }
}
