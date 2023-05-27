<?php

namespace Lib\Exception;

use Exception;
use Lib\Http\ErrorHandler;

class ExceptionHandler
{
    public static function handleException(Exception $exception)
    {
        if ($exception instanceof CustomException) {
            $errorCode = $exception->getErrorCode();
            $errorTitle = $exception->getErrorTitle();
            $errorMessage = $exception->getErrorMessage();
        } else {
            $errorCode = $exception->getCode();
            $errorTitle = 'Error';
            $errorMessage = $exception->getMessage();
        }

        ErrorHandler::renderError($errorCode, $errorTitle, $errorMessage);
    }
}
