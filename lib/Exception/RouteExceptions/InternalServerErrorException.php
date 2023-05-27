<?php

namespace Lib\Exception\RouteExceptions;

use Lib\Exception\CustomException;

class InternalServerErrorException extends CustomException
{
    public function __construct($message)
    {
        $errorCode = 500;
        $errorTitle = 'Internal Server Error';
        $errorMessage = $message;

        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
