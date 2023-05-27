<?php

namespace Lib\Exception\RouteExceptions;

use Lib\Exception\CustomException;

class MiddlewareException extends CustomException
{
    public function __construct($message)
    {
        $errorCode = 500;
        $errorTitle = 'Middleware Exception';
        $errorMessage = $message;
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
