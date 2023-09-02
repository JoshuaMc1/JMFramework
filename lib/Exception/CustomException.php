<?php

namespace Lib\Exception;

use Exception;

/**
 * Class CustomException
 * 
 * this is a custom exception class, used to handle errors that 
 * occur within the framework
 */
class CustomException extends Exception
{
    protected $errorCode;
    protected $errorTitle;
    protected $errorMessage;

    /**
     * CustomException constructor.
     *
     * @param mixed $errorCode    The unique error code associated with this exception.
     * @param string $errorTitle  The title or description of the error.
     * @param string $errorMessage The detailed error message.
     */
    public function __construct($errorCode, $errorTitle, $errorMessage)
    {
        parent::__construct();
        $this->errorCode = $errorCode;
        $this->errorTitle = $errorTitle;
        $this->errorMessage = $errorMessage;
    }

    /**
     * Get the unique error code associated with this exception.
     *
     * @return mixed The error code.
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Get the title or description of the error.
     *
     * @return string The error title.
     */
    public function getErrorTitle()
    {
        return $this->errorTitle;
    }

    /**
     * Get the detailed error message.
     *
     * @return string The error message.
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}
