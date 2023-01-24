<?php

namespace App\Controllers;

class Controller
{
    public function view($route, $data = [])
    {
        extract($data);

        $route = str_replace('.', '/', $route);

        $filePath = "../resources/views/{$route}.php";

        if (file_exists($filePath)) {
            ob_start();

            include $filePath;

            $content = ob_get_clean();

            return $content;
        } else {
            return 'El archivo no existe';
        }
    }
}
