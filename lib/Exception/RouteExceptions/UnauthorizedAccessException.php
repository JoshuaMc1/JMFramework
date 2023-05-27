<?php

namespace Lib\Exception\RouteExceptions;

use Lib\Exception\CustomException;

class UnauthorizedAccessException extends CustomException
{
    public function __construct($message = 'You are not authorized to access this resource.')
    {
        $errorCode = 401;
        $errorTitle = 'Unauthorized Access';
        $errorMessage = $message;
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
