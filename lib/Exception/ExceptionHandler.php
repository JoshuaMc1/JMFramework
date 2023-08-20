<?php

namespace Lib\Exception;

use Exception;
use Lib\Http\ErrorHandler;
use Lib\Support\Log;

class ExceptionHandler
{
    public static function handleException(Exception | \Throwable $exception)
    {
        if ($exception instanceof CustomException) {
            $errorCode = $exception->getErrorCode();
            $errorTitle = $exception->getErrorTitle();
            $errorMessage = $exception->getErrorMessage();
        } else {
            $errorCode = $exception->getCode();
            $errorTitle = 'Error ' . $exception->getCode();
            $errorMessage = $exception->getMessage();
        }

        Log::writeLog('local.exception', $errorMessage, [
            'code' => $errorCode,
            'stack_trace' => $exception->getTraceAsString(),
        ]);

        ErrorHandler::renderError($errorCode, $errorTitle, $errorMessage);
    }
}
