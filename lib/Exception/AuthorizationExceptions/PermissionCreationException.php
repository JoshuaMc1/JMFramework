<?php

namespace Lib\Exception\AuthorizationExceptions;

use Lib\Exception\CustomException;

/**
 * Class PermissionCreationException
 * 
 * this exception is thrown when an attempt is made to create a permission
 * that does not exist
 */
class PermissionCreationException extends CustomException
{
    /**
     * The function constructs an error message for a non-existent permission.
     * 
     * @param permissionId The permissionId parameter is the ID of the permission that is being
     * created.
     */
    public function __construct($permissionId)
    {
        $errorCode = 2201;
        $errorTitle = lang('exception.error_creating_permission');
        $errorMessage = lang('exception.error_creating_permission_message', ['permissionId' => $permissionId]);
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
