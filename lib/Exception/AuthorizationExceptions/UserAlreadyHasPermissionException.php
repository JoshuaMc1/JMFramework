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
        $errorCode = 2205;
        $errorTitle = lang('exception.user_already_has_permission');
        $errorMessage = lang('exception.user_already_has_permission_message', ['userId' => $userId, 'roleId' => $roleId]);
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
