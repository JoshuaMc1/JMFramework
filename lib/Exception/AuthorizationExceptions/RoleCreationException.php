<?php

namespace Lib\Exception\AuthorizationExceptions;

use Lib\Exception\CustomException;

/**
 * Class RoleCreationException
 * 
 * this exception is thrown when a role cannot be created 
 */
class RoleCreationException extends CustomException
{
    public function __construct($roleId)
    {
        $errorCode = 2203;
        $errorTitle = lang('error_creating_role');
        $errorMessage = lang('error_creating_role_message', ['roleId' => $roleId]);
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
