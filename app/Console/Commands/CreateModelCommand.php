<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateModelCommand extends Command
{
    protected $signature = 'create:model {name}';

    protected $description = 'Create a new model';

    public function handle()
    {
        $name = $this->argument('name');
        $filename = dirname(__DIR__, 3) . "/app/Models/{$name}.php";
        $pluralName = strtolower($this->pluralize($name));
        $stub = <<<EOD
        <?php

        namespace App\Models;

        use Lib\Model\Model;

        class $name extends Model
        {
            protected \$table = '$pluralName';
        }
        EOD;

        if (file_exists($filename)) {
            $this->error('The model already exists!');
        } else {
            file_put_contents($filename, $stub);
            $this->info('Model created correctly.');
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
