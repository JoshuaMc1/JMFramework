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
    public function __construct()
    {
        $errorCode = 1001;
        $errorTitle = 'File Upload Error';
        $errorMessage = 'Failed to upload file';

        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
