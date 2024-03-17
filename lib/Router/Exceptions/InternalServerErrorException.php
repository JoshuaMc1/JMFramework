<?php

namespace Lib\Router\Exceptions;

use Lib\Exception\CustomException;

/**
 * Class InternalServerErrorException
 * 
 * this exception is thrown when there is an internal server error or something else went wrong
 */
class InternalServerErrorException extends CustomException
{
    /**
     * The function is a constructor that sets the error code, title, and message for an internal
     * server error.
     * 
     * @param message The message parameter is a string that represents the specific error message that
     * you want to display. It can be any custom error message that you want to provide to the user.
     */
    public function __construct($message)
    {
        $errorCode = 500;
        $errorTitle = lang('exception.internal_server_error');
        $errorMessage = $message;

        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
