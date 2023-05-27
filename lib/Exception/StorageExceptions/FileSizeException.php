<?php

namespace Lib\Exception\StorageExceptions;

use Lib\Exception\CustomException;

class FileSizeException extends CustomException
{
    public function __construct()
    {
        $errorCode = 1004;
        $errorTitle = 'Invalid File Size';
        $errorMessage = 'The file size exceeds the allowed limit';

        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
