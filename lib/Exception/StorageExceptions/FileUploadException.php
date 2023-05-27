<?php

namespace Lib\Exception\StorageExceptions;

use Lib\Exception\CustomException;

class FileUploadException extends CustomException
{
    public function __construct()
    {
        $errorCode = 1001;
        $errorTitle = 'File Upload Error';
        $errorMessage = 'Failed to upload file';

        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
