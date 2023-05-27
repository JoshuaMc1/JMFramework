<?php

use Lib\Route;

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . "../../vendor/autoload.php";
require_once __DIR__ . '/../lib/Global/Global.php';
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/api.php';

Route::dispatch();
