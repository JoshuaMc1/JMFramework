<?php

namespace Lib\Exception\RouteExceptions;

use Lib\Exception\CustomException;

/**
 * Class InvalidRouteConfigurationException
 * 
 * this exception is thrown when there is an invalid route configuration
 */
class InvalidRouteConfigurationException extends CustomException
{
    /**
     * The function constructs an object with an error code, title, and message.
     * 
     * @param message The message parameter is a string that represents the specific error message for
     * the exception. It is used to provide additional information about the error that occurred.
     */
    public function __construct($message)
    {
        $errorCode = 500;
        $errorTitle = 'Invalid Route Configuration';
        $errorMessage = $message;
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
