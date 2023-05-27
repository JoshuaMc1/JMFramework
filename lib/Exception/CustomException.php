<?php

namespace Lib\Exception;

use Exception;

class CustomException extends Exception
{
    protected $errorCode;
    protected $errorTitle;
    protected $errorMessage;

    public function __construct($errorCode, $errorTitle, $errorMessage)
    {
        parent::__construct();
        $this->errorCode = $errorCode;
        $this->errorTitle = $errorTitle;
        $this->errorMessage = $errorMessage;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getErrorTitle()
    {
        return $this->errorTitle;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}
