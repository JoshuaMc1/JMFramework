<?php

namespace Lib\Exception\AuthorizationExceptions;

use Lib\Exception\CustomException;

class PermissionNotFoundException extends CustomException
{
    public function __construct($permissionId)
    {
        $errorCode = 404;
        $errorTitle = 'Permission not found';
        $errorMessage = "The permission `{$permissionId}` does not exist.";
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
