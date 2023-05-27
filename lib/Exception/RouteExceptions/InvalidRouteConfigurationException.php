<?php

namespace Lib\Exception\RouteExceptions;

use Lib\Exception\CustomException;

class InvalidRouteConfigurationException extends CustomException
{
    public function __construct($message)
    {
        $errorCode = 500;
        $errorTitle = 'Invalid Route Configuration';
        $errorMessage = $message;
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
