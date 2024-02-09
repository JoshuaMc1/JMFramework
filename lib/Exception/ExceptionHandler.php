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

        ErrorHandler::renderError($errorCode, $errorTitle, $errorMessage);
    }
}
