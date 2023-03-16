<?php

namespace App\Controllers;

class WelcomeController extends Controller
{
    public function welcome()
    {
        return $this->view('welcome');
    }
}
