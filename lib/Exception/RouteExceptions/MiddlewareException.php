<?php

namespace Lib\Exception\RouteExceptions;

use Lib\Exception\CustomException;

/**
 * Class MiddlewareException
 * 
 * this exception is thrown when a middleware fails
 */
class MiddlewareException extends CustomException
{
    /**
     * The function is a constructor for a class that sets the error code, title, and message for a
     * middleware exception.
     * 
     * @param message The "message" parameter is a string that represents the error message that will
     * be displayed when the exception is thrown. It should provide a clear and concise description of
     * the error that occurred.
     */
    public function __construct($message)
    {
        $errorCode = 500;
        $errorTitle = 'Middleware Exception';
        $errorMessage = $message;
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
