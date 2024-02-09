<?php

namespace Lib\Exception\AuthorizationExceptions;

use Lib\Exception\CustomException;

/**
 * Class RoleNotFoundException
 * 
 * this exception is thrown when a role cannot be found
 */
class RoleNotFoundException extends CustomException
{
    /**
     * The function constructs an error message for a role that is not found.
     * 
     * @param roleId The roleId parameter is the ID of the role that is not found.
     */
    public function __construct($roleId)
    {
        $errorCode = 2204;
        $errorTitle = lang('role_not_found');
        $errorMessage = lang('role_not_found_message', ['roleId' => $roleId]);
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
