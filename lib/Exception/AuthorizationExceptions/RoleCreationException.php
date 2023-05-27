<?php

namespace Lib\Exception\AuthorizationExceptions;

use Lib\Exception\CustomException;

class RoleCreationException extends CustomException
{
    public function __construct($roleId)
    {
        $errorCode = 404;
        $errorTitle = 'Error creating the role';
        $errorMessage = "The role with ID {$roleId} does not exist.";
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
