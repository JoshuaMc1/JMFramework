<?php

return [

    /**
     * The views directory
     * 
     * @var string
     */
    'paths' => [
        view_path()
    ],

    /**
     * The compiled views directory
     * 
     * @var string
     * */
    'compiled' => realpath(cache_path() . '/views'),

    /**
     * Auto reload the view when it changes
     * 
     * Default: true
     * 
     * @var bool
     */
    'auto_reload' => true,

    /**
     * Optimizations for the view compiler
     * 
     * Default: -1
     * 
     * @var int
     */
    'optimizations' => -1,

    /**
     * Extensions for the view
     * 
     * Default: ['.php.twig']
     */
    'extensions' => ['.php.twig'],
];
