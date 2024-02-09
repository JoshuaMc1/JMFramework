<?php

namespace App\Console\Commands;

require_once __DIR__ . '/../../../lib/Global/Global.php';

use Illuminate\Console\Command;
use Lib\Database\ColumnDefinition;
use Lib\Database\Contracts\Schema;
use Lib\Support\Env;

class SchemaCommand extends Command
{
    protected $signature = 'schema:run';

    protected $description = 'Execute the schema forge for the database.';

    public function handle()
    {
        try {
            Env::load();

            $directory = database_path();
            $this->processSchemaFiles($directory);

            $this->showSuccessMessage('- Schema executed successfully.');
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

    private function processSchemaFiles(string $directory): void
    {
        $files = array_diff(scandir($directory), ['.', '..']);

        $this->executeSchemaFile($directory, $files);
    }

    private function isValidSchemaFile(string $file): bool
    {
        return pathinfo($file, PATHINFO_EXTENSION) === 'php';
    }

    private function executeSchemaFile(string $directory, array $files): void
    {
        $schema = null;
        $messages = [];

        $this->comment("Executing schema files...");
        $this->line("");

        foreach (array_reverse($files) as $file) {
            if ($this->isValidSchemaFile($file)) {
                $schema = require $directory . '/' . $file;

                if (!$schema instanceof Schema) {
                    $messages[] = [
                        "schema" => $file,
                        "message" => "Not a valid schema",
                        "status" => "[FAIL]",
                    ];
                } else {
                    $this->executeDown($schema);

                    $messages[] = [
                        "schema" => $file,
                        "message" => "Down executed successfully",
                        "status" => "[OK]",
                    ];
                }
            }
        }

        foreach ($files as $file) {
            if ($this->isValidSchemaFile($file)) {
                $schema = require $directory . '/' . $file;

                if (!$schema instanceof Schema) {
                    $messages[] = [
                        "schema" => $file,
                        "message" => "Not a valid schema",
                        "status" => "[FAIL]",
                    ];
                } else {
                    $this->executeUp($schema);

                    $messages[] = [
                        "schema" => $file,
                        "message" => "Up executed successfully",
                        "status" => "[OK]",
                    ];
                }
            }
        }

        $this->showMessages($messages);
    }

    private function executeDown(Schema $schema)
    {
        $schema->down();
    }

    private function executeUp(Schema $schema)
    {
        $schema->up(new ColumnDefinition());
    }

    private function showMessages(array $messages)
    {
        $headers = ['No.', 'Schema', 'Message', 'Status'];
        $tableData = [];

        $i = 1;

        foreach ($messages as $message) {
            $tableData[] = [$i++, $message['schema'], $message['message'], $message['status']];
        }

        $this->table($headers, $tableData);
    }
}
