<?php

namespace Lib\Exception\StorageExceptions;

use Lib\Exception\CustomException;

/**
 * Class FileSizeException
 * 
 * this exception is thrown when the file size exceeds the allowed limit
 */
class FileSizeException extends CustomException
{
    /**
     * The function constructs an object with an error code, title, and message for an invalid file
     * size.
     */
    public function __construct()
    {
        $errorCode = 1004;
        $errorTitle = 'Invalid File Size';
        $errorMessage = 'The file size exceeds the allowed limit';

        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
