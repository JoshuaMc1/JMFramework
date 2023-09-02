<?php

namespace Lib\Exception\RouteExceptions;

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
    public function __construct()
    {
        $errorCode = 405;
        $errorTitle = 'Method Not Allowed';
        $errorMessage = 'The requested method is not allowed for this route.';
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
