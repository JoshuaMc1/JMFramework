<?php

namespace App\Controllers;

class Controller
{
    /**
     * It takes a route and an array of data, extracts the data, and returns the content of the file
     * 
     * @param route The route to the view file.
     * @param data This is an array of data that you want to pass to the view.
     * 
     * @return The content of the view.
     */
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
