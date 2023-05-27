<?php

namespace Lib\Exception\AuthorizationExceptions;

use Lib\Exception\CustomException;

class RoleNotFoundException extends CustomException
{
    public function __construct($roleId)
    {
        $errorCode = 404;
        $errorTitle = 'Role not found';
        $errorMessage = "Role with ID {$roleId} does not exist.";
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
