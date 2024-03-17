<?php

namespace Lib\Exception\StorageExceptions;

use Lib\Exception\CustomException;

/**
 * Class FileUploadException
 * 
 * this exception is thrown when file upload fails
 */
class FileUploadException extends CustomException
{
    /**
     * This function constructs an object with an error code, title, and message for a file upload
     * error.
     */
    public function __construct($title = null, $message = null)
    {
        $errorCode = 0204;
        $errorTitle = $title ?? lang('exception.file_upload_error');
        $errorMessage = $message ?? lang('exception.file_upload_error_message');

        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
