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
         * 
         * @example
         * 
         * BaseKernel::$additionalRoutes = [
         *    sprintf('%s/admin.php', routes_path()),
         *    ...
         *  ]
         */
        BaseKernel::$additionalRoutes = [];

        /**
         * Register the routes for the application.
         * 
         * @see \Lib\Kernel\BaseKernel
         */
        BaseKernel::register();
    }
}
