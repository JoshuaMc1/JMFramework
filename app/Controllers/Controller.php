<?php

namespace App\Controllers;

class Controller
{
    public function view(string $route, array $data = []): string
    {
        extract($data, EXTR_OVERWRITE | EXTR_PREFIX_ALL, 'data');

        $viewPath = str_replace('.', '/', $route);
        $viewFile = "../resources/views/{$viewPath}.php";

        if (!file_exists($viewFile)) {
            return 'El archivo no existe';
        }

        ob_start();
        include $viewFile;
        return ob_get_clean();
    }
}
