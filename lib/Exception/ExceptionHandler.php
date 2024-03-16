<?php

namespace Lib\Exception;

use Exception;
use Lib\Http\ErrorHandler;
use Lib\Support\Log;
use Throwable;

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
     * @param CustomException|Exception|Throwable $exception The exception or error to be handled.
     */
    public static function handleException(CustomException | Exception | Throwable $exception)
    {
        $errorCode = $exception instanceof CustomException ?
            ($exception->getErrorCode() ?? 500) : ($exception->getCode() ?: 500);

        $errorTitle = $exception instanceof CustomException ?
            $exception->getErrorTitle() :
            lang('an_error_occurred');

        $errorMessage = $exception instanceof CustomException ?
            $exception->getErrorMessage() : ($exception->getMessage() ?: lang('an_error_occurred'));

        Log::debug($exception, $errorMessage);

        if ($errorCode === 404) {
            ErrorHandler::renderError($errorCode, $errorTitle, $errorMessage);
            return;
        }

        if ($errorCode === 401) {
            ErrorHandler::renderError($errorCode, $errorTitle, $errorMessage);
            return;
        }

        (config('app.env') === 'local') ?
            ErrorHandler::renderError($errorCode, $errorTitle, $errorMessage) :
            ErrorHandler::renderError(500, 'Internal Server Error', 'An internal server error occurred.');
    }
}
