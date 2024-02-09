<?php

return [

    /**
     * Defaults cors paths  
     * 
     * Default: ['api/*'] - for all paths
     */
    'paths' => ['api/*'],

    /**
     * Cors allowed methods
     * 
     * Default: '*' - for all methods
     * */
    'allowed_methods' => ['*'],

    /**
     * Cors allowed origins
     * 
     * Default: '*' - for all origins
     * */
    'allowed_origins' => ['*'],

    /**
     * Cors allowed origins patterns
     * 
     * Default: []
     * */
    'allowed_origins_patterns' => [],

    /**
     * Cors allowed headers
     * 
     * Default: '*' - for all headers
     * */
    'allowed_headers' => ['*'],

    /**
     * Cors exposed headers
     * 
     * Default: '*' - for all headers
     * */
    'exposed_headers' => [],

    /**
     * Cors max age
     * 
     * Default: '0' - for no max age
     * */
    'max_age' => 0,

    /**
     * Cors supports credentials
     * 
     * 'false' for no credentials
     * */
    'supports_credentials' => false,
];
