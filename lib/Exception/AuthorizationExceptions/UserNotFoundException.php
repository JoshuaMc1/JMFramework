<?php

namespace Lib\Exception\AuthorizationExceptions;

use Lib\Exception\CustomException;

/**
 * Class UserAlreadyHasPermissionException
 * 
 * this exception is thrown when a user already has a permission
 */
class UserNotFoundException extends CustomException
{
    /**
     * The function constructs an error message for a user not found.
     * 
     * @param userId The userId parameter is the unique identifier of the user that is being searched
     * for.
     */
    public function __construct($userId)
    {
        $errorCode = 2206;
        $errorTitle = lang('user_not_found');
        $errorMessage = lang('user_not_found_message', ['userId' => $userId]);
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
