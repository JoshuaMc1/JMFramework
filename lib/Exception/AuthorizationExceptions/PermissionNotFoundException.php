<?php

namespace Lib\Exception\AuthorizationExceptions;

use Lib\Exception\CustomException;

/**
 * Class PermissionNotFoundException
 * 
 * this exception is thrown when a permission is not found
 */
class PermissionNotFoundException extends CustomException
{
    /**
     * The function constructs an error object with a specific error code, title, and message.
     * 
     * @param permissionId The permissionId parameter is the identifier of the permission that is not
     * found.
     */
    public function __construct($permissionId)
    {
        $errorCode = 2202;
        $errorTitle = lang('exception.permission_not_found');
        $errorMessage = lang('exception.permission_not_found_message', ['permissionId' => $permissionId]);
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
