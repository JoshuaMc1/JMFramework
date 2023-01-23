<?php

namespace App\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return $this->view('home');
    }

    public function courses($id)
    {
        return 'El curso es: ' . $id;
    }
}
