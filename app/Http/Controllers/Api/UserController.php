<?php

namespace App\Http\Controllers\Api;

use Lib\Http\Auth;

class UserController
{
    public function index()
    {
        return response()->json([
            'user' => Auth::user('api'),
        ])->send();
    }
}
