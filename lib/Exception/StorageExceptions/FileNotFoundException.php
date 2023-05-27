<?php

namespace Lib\Exception\StorageExceptions;

use Lib\Exception\CustomException;

class FileNotFoundException extends CustomException
{
    public function __construct()
    {
        $errorCode = 1003;
        $errorTitle = 'File Not Found';
        $errorMessage = 'The requested file was not found';

        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
