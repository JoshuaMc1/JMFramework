<?php

namespace App\Console\Commands;

require_once __DIR__ . '/../../../lib/Global/Global.php';

use Illuminate\Console\Command;
use Lib\Support\Date;

class SchemaCreateCommand extends Command
{
    protected $signature = 'create:schema {schema}';

    protected $description = 'Create a new schema in the database';

    public function handle()
    {
        try {
            $name = $this->argument('schema');
            $fileName = $this->generateFileName($name);

            $this->comment("Creating schema...");

            $schemaFiles = scandir(database_path());
            $existingSchema = false;

            foreach ($schemaFiles as $file) {
                if ($this->getSchemaBaseName($file) === $this->getSchemaBaseName($fileName)) {
                    $existingSchema = true;
                    break;
                }
            }

            ($existingSchema) ?
                $this->showErrorMessage("- Schema {$name} already exists") :
                $this->createFile($name, $fileName);
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

    public function createFile(string $name, string $fileName)
    {
        file_put_contents(database_path() . '/' . $fileName, $this->generateStub($name));

        $this->showSuccessMessage("- Schema {$name} created successfully at database/{$fileName}");
    }

    private function getSchemaBaseName($fileName)
    {
        return preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $fileName);
    }

    public function generateFileName($name)
    {
        return Date::now()->format('Y_m_d_His') . '_' . $name . '.php';
    }

    public function generateStub($name)
    {
        $name = strtolower($name);

        return <<<EOD
            <?php

            use Lib\Database\ColumnDefinition;
            use Lib\Database\Contracts\Schema;
            use Lib\Database\SchemaForge;

            return new class implements Schema
            {
                public function up(ColumnDefinition \$column): void
                {
                    SchemaForge::createTable('{$name}', [
                        \$column->id()->exec(),
                        \$column->timestamps()->exec(),
                    ]);
                }

                public function down(): void
                {
                    SchemaForge::dropTable('{$name}');
                }
            };
            EOD;
    }
}
