<?php

namespace Lib\Exception\StorageExceptions;

use Lib\Exception\CustomException;

/**
 * Class FileDeleteException
 * 
 * this exception is thrown when file deletion fails 
 */
class FileDeleteException extends CustomException
{
    /**
     * The function is a constructor that sets error code, title, and message for a file delete error.
     */
    public function __construct()
    {
        $errorCode = 0201;
        $errorTitle = lang('file_delete_error');
        $errorMessage = lang('file_delete_error_message');

        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
