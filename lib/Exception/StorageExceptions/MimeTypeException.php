<?php

namespace Lib\Exception\StorageExceptions;

use Lib\Exception\CustomException;

class MimeTypeException extends CustomException
{
    public function __construct()
    {
        $errorCode = 1005;
        $errorTitle = 'Invalid Mime Type';
        $errorMessage = 'The file has an invalid mime type';

        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
