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
        $errorCode = 404;
        $errorTitle = 'Role not found';
        $errorMessage = "Role with ID {$roleId} does not exist.";
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
