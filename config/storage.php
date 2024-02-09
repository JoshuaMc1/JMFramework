<?php

return [

    /**
     * Default storage path
     * 
     * The path of the storage
     * */
    'path' => storage_path(),

    /**
     * Default storage allowed types
     * 
     * The allowed types of the storage
     * 
     * Add more types if you need
     * */
    'allowed_types' => [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'video/mp4',
        'video/mpeg',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation'
    ]
];
