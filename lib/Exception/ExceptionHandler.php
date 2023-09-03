<?php

namespace Lib\Exception;

use Exception;
use Lib\Http\ErrorHandler;
use Lib\Support\Log;

/**
 * Class ExceptionHandler
 * 
 * this is a custom exception class, used to handle errors that occur within the application
 */
class ExceptionHandler
{
    /**
     * Handle Exception
     *
     * This method is responsible for handling exceptions and throwable errors
     * within the application. It logs the error details and renders an error page.
     *
     * @param Exception|\Throwable $exception The exception or error to be handled.
     */
    public static function handleException(Exception | \Throwable $exception)
    {
        // Determine the error code, title, and message based on the exception type.
        if ($exception instanceof CustomException) {
            $errorCode = $exception->getErrorCode();
            $errorTitle = $exception->getErrorTitle();
            $errorMessage = $exception->getErrorMessage();
        } else {
            $errorCode = $exception->getCode();
            $errorTitle = 'Catch Exception - ' . $exception->getCode();
            $errorMessage = $exception->getMessage();
        }

        // Log the error details, including code and stack trace.
        Log::writeLog('local.exception', $errorMessage, [
            'code' => $errorCode,
            'stack_trace' => $exception->getTraceAsString(),
        ]);

        // Render an error page using the ErrorHandler class.
        ErrorHandler::renderError($errorCode, $errorTitle, $errorMessage);
    }
}
