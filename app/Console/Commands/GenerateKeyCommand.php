<?php

namespace App\Console\Commands;

require_once __DIR__ . '/../../../lib/Global/Global.php';

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateKeyCommand extends Command
{
    protected $signature = 'key:generate';

    protected $description = 'Generate an application key.';

    public function handle()
    {
        try {
            $envPath = $this->getEnvPath();
            $key = $this->generateKey();

            if ($this->updateEnvFile($envPath, $key)) {
                $this->info('Application key generated successfully and updated in .env file.');
            } else {
                $this->error('Failed to update the application key in .env file.');
            }
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
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

    private function generateKey()
    {
        $id = Str::random(10);
        $random = Str::random(90);
        return $id . '|' . $random;
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
