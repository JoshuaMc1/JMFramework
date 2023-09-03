<?php

namespace Lib\Exception\StorageExceptions;

use Lib\Exception\CustomException;

/**
 * Class FileNotFoundException
 * 
 * this exception is thrown when a file is not found
 */
class FileNotFoundException extends CustomException
{
    /**
     * The function constructs an object with an error code, title, and message.
     */
    public function __construct()
    {
        $errorCode = 1003;
        $errorTitle = 'File Not Found';
        $errorMessage = 'The requested file was not found';

        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
