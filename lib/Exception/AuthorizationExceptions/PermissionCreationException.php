<?php

namespace Lib\Exception\AuthorizationExceptions;

use Lib\Exception\CustomException;

class PermissionCreationException extends CustomException
{
    public function __construct($permissionId)
    {
        $errorCode = 404;
        $errorTitle = 'Error creating the permission';
        $errorMessage = "The permission with ID {$permissionId} does not exist.";
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
