<?php

namespace Lib\Exception\StorageExceptions;

use Lib\Exception\CustomException;

/**
 * Class MimeTypeException
 * 
 * this exception is thrown when the file has an invalid or disallowed mime type
 */
class MimeTypeException extends CustomException
{
    /**
     * The function constructs an object with an error code, title, and message.
     */
    public function __construct()
    {
        $errorCode = 1005;
        $errorTitle = 'Invalid Mime Type';
        $errorMessage = 'The file has an invalid mime type';

        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
