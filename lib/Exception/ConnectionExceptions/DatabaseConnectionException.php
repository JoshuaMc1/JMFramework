<?php

namespace Lib\Exception\ConnectionExceptions;

use Lib\Exception\CustomException;

class DatabaseConnectionException extends CustomException
{
    public function __construct($errorCode = 500, $errorTitle = 'Internal Server Error', $errorMessage = 'There was an error connecting to the database')
    {
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
