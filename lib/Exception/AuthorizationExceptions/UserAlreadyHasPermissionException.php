<?php

namespace Lib\Exception\AuthorizationExceptions;

use Lib\Exception\CustomException;

/**
 * Class UserAlreadyHasPermissionException
 * 
 * this exception is thrown when a user already has a permission 
 */
class UserAlreadyHasPermissionException extends CustomException
{
    /**
     * The function constructs an error message stating that a user already has a specific permission.
     * 
     * @param userId The ID of the user who already has the permission.
     * @param roleId The roleId parameter represents the ID of the permission that the user already
     * has.
     */
    public function __construct($userId, $roleId)
    {
        $errorCode = 404;
        $errorTitle = 'User already has permission';
        $errorMessage = "The user with ID {$userId} already has the permission with ID {$roleId}.";
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}