<?php

return [

    /**
     * Default session path
     * 
     * The path of the session
     * */
    'path' => session_path(),

    /**
     * Default session lifetime
     * 
     * The lifetime of the session
     * 
     * In seconds
     * 
     * Default: 86400 (24 hours)
     * */
    'lifetime' => 86400,

    /**
     * Default session probability
     * 
     * The probability of the session
     * 
     * In number
     * 
     * Default: 1 (100%)
     * */
    'probability' => 1,

    /**
     * Default session divisor
     * 
     * The divisor of the session
     * 
     * In number
     * 
     * Default: 100 (1%)
     * */
    'divisor' => 100,
];
