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
        $errorCode = 0202;
        $errorTitle = lang('exception.file_not_found');
        $errorMessage = lang('exception.file_not_found_message');

        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
