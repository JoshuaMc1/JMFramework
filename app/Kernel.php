<?php

namespace App;

use Lib\Kernel\BaseKernel;
use Lib\Kernel\Contracts\KernelInterface;

/**
 * The Kernel class is the main entry point of the application.
 *
 * @package App
 */
class Kernel implements KernelInterface
{
    /**
     * Bootstraps the application.
     * 
     * @return void
     */
    public static function boot()
    {
        BaseKernel::boot();
    }
}
