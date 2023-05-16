<?php

namespace App\Controllers;

use function Lib\Global\view;

class WelcomeController
{
    public function welcome()
    {
        return view('welcome');
    }
}
