<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateKeyCommand extends Command
{
    protected $signature = 'key:generate';

    protected $description = 'Generate an application key.';

    public function handle()
    {
        try {
            $envPath = dirname(__DIR__, 3) . '/config/env.php';

            $key = $this->generateKey();

            if ($this->updateEnvFile($envPath, $key)) {
                $this->info('Application key generated successfully and updated in env.php');
            } else {
                $this->error('Failed to update the application key in env.php');
            }
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
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

        $updatedContent = preg_replace('/(define\(\'APP_KEY\',\s+\').*?(\'\);)/', '$1' . $key . '$2', $envContent);

        if ($updatedContent === null) {
            return false;
        }

        return file_put_contents($envPath, $updatedContent) !== false;
    }
}
