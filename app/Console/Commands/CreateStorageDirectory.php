<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateStorageDirectory extends Command
{
    protected $signature = 'storage:directory';

    protected $description = 'Crear un directorio de almacenamiento público.';

    public function handle()
    {
        $rootPath = dirname(__DIR__, 3);

        $publicPath = $rootPath . '/public';

        $directoryPath = $publicPath . '/storage';

        if (is_dir($directoryPath)) {
            $this->error('La carpeta ya existe.');
            return;
        }

        if (mkdir($directoryPath, 0666)) {
            $this->info('La carpeta se ha creado correctamente.');
        } else {
            $this->error('Ha ocurrido un error al crear la carpeta.');
        }
    }
}
