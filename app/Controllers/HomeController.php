<?php

namespace App\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Home',
            'description' => 'Pagina home'
        ];

        return $this->view('home', $data);
    }

    public function courses($id)
    {
        return 'El curso es: ' . $id;
    }
}
