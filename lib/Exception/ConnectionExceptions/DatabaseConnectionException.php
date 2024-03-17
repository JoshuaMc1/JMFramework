<?php

namespace Lib\Exception\ConnectionExceptions;

use Lib\Exception\CustomException;

/**
 * Class DatabaseConnectionException
 * 
 * this exception is thrown when there is an error connecting to the database
 */
class DatabaseConnectionException extends CustomException
{
    /**
     * The function constructs an error object with default values for error code, title, and message.
     * 
     * @param errorCode The errorCode parameter is used to specify the error code for the error. In
     * this case, the default value is 500, which typically represents an internal server error.
     * However, you can pass a different error code if needed.
     * @param errorTitle The error title is a string that represents the title or name of the error. It
     * is typically used to provide a brief description of the error that occurred. In this case, the
     * default value is 'Internal Server Error'.
     * @param errorMessage The errorMessage parameter is a string that represents the specific error
     * message that occurred. In this case, it is set to 'There was an error connecting to the
     * database'.
     */
    public function __construct($errorCode = 500, $errorMessage = 'There was an error connecting to the database')
    {
        parent::__construct($errorCode, lang('exception.database_connection_error'), $errorMessage);
    }
}
