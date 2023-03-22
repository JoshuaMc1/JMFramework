<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateModel extends Command
{
    protected $signature = 'model {name}';

    protected $description = 'Crear un nuevo modelo';

    public function handle()
    {
        $name = $this->argument('name');
        $filename = dirname(__DIR__, 3) . "/app/Models/{$name}.php";
        $pluralName = strtolower($this->pluralize($name));
        $stub = <<<EOD
        <?php

        namespace App\Models;

        class $name extends Model
        {
            protected \$table = '$pluralName';
        }
        EOD;

        if (file_exists($filename)) {
            $this->error('El modelo ya existe!');
        } else {
            file_put_contents($filename, $stub);
            $this->info('Modelo creado correctamente.');
        }
    }

    function pluralize($word)
    {
        $irregulars = array(
            'man' => 'men',
            'woman' => 'women',
            'child' => 'children',
            'tooth' => 'teeth',
            'foot' => 'feet',
            'person' => 'people',
            'gentleman' => 'gentlemen',
            'knife' => 'knives'
        );

        $last_char = substr($word, -1);

        if (array_key_exists($word, $irregulars)) {
            return $irregulars[$word];
        } elseif ($last_char == 'y') {
            return substr($word, 0, -1) . 'ies';
        } elseif (in_array($last_char, array('s', 'x', 'z'))) {
            return $word . 'es';
        } else {
            return $word . 's';
        }
    }
}
