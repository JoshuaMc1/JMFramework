<?php

namespace App\Console\Commands;

require_once __DIR__ . '/../../../lib/Global/Global.php';

use Illuminate\Console\Command;

class CreateModelCommand extends Command
{
    protected $signature = 'create:model {name}';

    protected $description = 'Create a new model';

    public function handle()
    {
        try {
            $name = $this->argument('name');
            $filename = $this->getModelFilePath($name);

            $directory = $this->getDirectoryFromFilePath($filename);
            $this->createDirectory($directory);

            $namespace = $this->getNamespace($name);
            $tableName = $this->pluralize($this->getTableName($name));
            $className = $this->getClassName($name);
            $stub = $this->getModelStub($namespace, $className, $tableName);

            $this->comment('Creating model...');

            $this->modelExists($filename)
                ? $this->showErrorMessage('- The model already exists!')
                : (
                    file_put_contents($filename, $stub) &&
                    $this->showSuccessMessage('- Model has been successfully created!')
                );
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

    protected function modelExists($filename)
    {
        return file_exists($filename);
    }

    protected function getModelFilePath($name)
    {
        return model_path() . '/' . $this->getModelPath($name) . '.php';
    }

    protected function getModelPath($name)
    {
        return ltrim(str_replace('\\', '/', $name), '/');
    }

    protected function getModelStub($namespace, $className, $tableName)
    {
        return <<<EOD
            <?php

            namespace App\Models{$namespace};

            use Lib\Model\Model;

            class $className extends Model
            {
                protected \$table = '$tableName';
            }
            EOD;
    }

    protected function getNamespace($name)
    {
        $parts = explode('/', $name);
        return count($parts) > 1 ? '\\' . implode('\\', array_slice($parts, 0, -1)) : '';
    }

    protected function getTableName($name)
    {
        return strtolower($this->getClassName($name));
    }

    protected function getClassName($name)
    {
        $parts = explode('/', $name);
        return ucfirst(end($parts));
    }

    protected function createDirectory($directory)
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    protected function getDirectoryFromFilePath($filename)
    {
        return dirname($filename);
    }

    protected function pluralize($word)
    {
        $irregulars = [
            'man' => 'men',
            'woman' => 'women',
            'child' => 'children',
            'tooth' => 'teeth',
            'foot' => 'feet',
            'person' => 'people',
            'gentleman' => 'gentlemen',
            'knife' => 'knives'
        ];

        $lastChar = substr($word, -1);

        if (array_key_exists($word, $irregulars)) {
            return $irregulars[$word];
        }

        if ($lastChar == 'y') {
            return substr($word, 0, -1) . 'ies';
        }

        if (in_array($lastChar, ['s', 'x', 'z'])) {
            return $word . 'es';
        }

        return $word . 's';
    }
}
