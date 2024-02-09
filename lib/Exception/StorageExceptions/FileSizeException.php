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
        $errorCode = 0203;
        $errorTitle = lang('invalid_file_size');
        $errorMessage = lang('invalid_file_size_message');

        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
