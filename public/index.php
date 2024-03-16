<?php

use App\Kernel;

/**
 * Load Composer Autoloader
 */
require_once __DIR__ . "../../vendor/autoload.php";

/**
 * Bootstrap the Kernel 
 * 
 * @return void
 */
Kernel::boot();

/**
 * Register the Kernel
 * 
 * @return void
 */
Kernel::register();
