<?php

namespace Lib\Exception\StorageExceptions;

use Lib\Exception\CustomException;

class FileDeleteException extends CustomException
{
    public function __construct()
    {
        $errorCode = 1002;
        $errorTitle = 'File Delete Error';
        $errorMessage = 'Failed to delete file';

        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
