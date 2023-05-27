<?php

namespace Lib\Exception\AuthorizationExceptions;

use Lib\Exception\CustomException;

class UserNotFoundException extends CustomException
{
    public function __construct($userId)
    {
        $errorCode = 404;
        $errorTitle = 'User not found';
        $errorMessage = "The user with ID {$userId} does not exist.";
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
