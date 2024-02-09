<?php

namespace Lib\Router\Exceptions;

use Lib\Exception\CustomException;

/**
 * Class MethodNotAllowedException
 * 
 * this exception is thrown when the requested method is not allowed for this route
 */
class MethodNotAllowedException extends CustomException
{
    /**
     * The function constructs an error object with a specific error code, title, and message.
     */
    public function __construct($method = 'GET', $route = null)
    {
        $errorCode = 405;
        $errorTitle = 'Method Not Allowed';
        $errorMessage = "The $method method is not supported for route $route.";
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
