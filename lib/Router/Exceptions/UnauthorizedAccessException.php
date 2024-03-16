<?php

namespace Lib\Router\Exceptions;

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
    public function __construct($message = null)
    {
        $errorCode = 401;
        $errorTitle = lang('unauthorized_access');
        $errorMessage = $message ?? lang('unauthorized_access_message');
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
