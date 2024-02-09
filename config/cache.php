<?php

return [

    /**
     * Default cache path
     * 
     * The path of the cache
     */
    'path' => cache_path(),

    /**
     * Default cache time to live
     * 
     * The time to live of the cache
     * 
     * In seconds
     * 
     * Default: 7200 (2 hours)
     */
    'ttl' => 7200,

    /**
     * Secure cache data
     * 
     * Indicates whether cache data should be encrypted for security reasons. 
     * It is not recommended to enable this option during development, as 
     * encrypted data may hinder debugging and consume more resources.
     * 
     * Default: false
     */
    'secure' => false
];
