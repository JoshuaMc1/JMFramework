<?php

namespace Lib\Exception\RouteExceptions;

use Lib\Exception\CustomException;

class MethodNotAllowedException extends CustomException
{
    public function __construct()
    {
        $errorCode = 405;
        $errorTitle = 'Method Not Allowed';
        $errorMessage = 'The requested method is not allowed for this route.';
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
