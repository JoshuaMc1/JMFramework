<?php

namespace Lib\Exception\AuthorizationExceptions;

use Lib\Exception\CustomException;

class UserAlreadyHasPermissionException extends CustomException
{
    public function __construct($userId, $roleId)
    {
        $errorCode = 404;
        $errorTitle = 'User already has permission';
        $errorMessage = "The user with ID {$userId} already has the permission with ID {$roleId}.";
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
