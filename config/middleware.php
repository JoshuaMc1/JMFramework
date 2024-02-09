<?php

return [

    /**
     * The default middleware that should be used in the application.
     * 
     * @var array
     */
    'default' => [
        App\Http\Middleware\VerifyCsrfToken::class,
    ],

    /**
     * The middleware that should be excluded from CSRF verification.
     * 
     * @var array
     * 
     * example:
     * 
     * 'exclude_prefixes' => [
     *      '/api',
     *      '/admin',
     *      '/user',
     *  ],
     * */
    'exclude_prefixes' => [
        '/api',
    ],
];
