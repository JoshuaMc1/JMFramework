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
        /**
         * Bootstraps the application.
         * 
         * @see \Lib\Kernel\BaseKernel
         */
        BaseKernel::boot();
    }

    /**
     * Registers the routes for the application.
     * 
     * @return void
     */
    public static function register()
    {
        /**
         * Add additional routes
         * 
         * @see \Lib\Kernel\BaseKernel
         * 
         * @var array
         */
        BaseKernel::$additionalRoutes = [
            // sprintf('%s/api.php', routes_path()),
        ];

        /**
         * Register the routes for the application.
         * 
         * @see \Lib\Kernel\BaseKernel
         */
        BaseKernel::register();
    }
}
