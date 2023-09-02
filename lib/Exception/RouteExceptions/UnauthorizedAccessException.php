<?php

namespace Lib\Exception\RouteExceptions;

use Lib\Exception\CustomException;

/**
 * Class UnauthorizedAccessException
 * 
 * this exception is thrown when the user is not authorized to access the resource
 */
class UnauthorizedAccessException extends CustomException
{
    /**
     * The function constructs an instance of a class with an error message for unauthorized access.
     * 
     * @param message The message parameter is a string that represents the error message to be
     * displayed when the user is not authorized to access a resource. By default, the message is set
     * to 'You are not authorized to access this resource.', but it can be customized by passing a
     * different string as an argument when creating an instance
     */
    public function __construct($message = 'You are not authorized to access this resource.')
    {
        $errorCode = 401;
        $errorTitle = 'Unauthorized Access';
        $errorMessage = $message;
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
