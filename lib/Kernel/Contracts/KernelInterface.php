<?php

namespace Lib\Kernel\Contracts;

interface KernelInterface
{
    /**
     * Bootstraps the application.
     *
     * @return void
     */
    public static function boot();

    /**
     * Registers the routes for the application.
     *
     * @return void
     */
    public static function register();
}
