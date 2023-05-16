<?php

namespace App\Controllers;

use App\Models\Contact;
use Lib\Support\Cache;

use function Lib\Global\view;

class PruebaController
{
    public function index()
    {
        if (Cache::has('contacts')) {
            $contacts = Cache::get('contacts');
        } else {
            $contacts = Contact::all();
            Cache::set('contacts', $contacts, 14400);
        }

        $contacts = Contact::all();

        return view('contact', compact('contacts'));
    }
}
