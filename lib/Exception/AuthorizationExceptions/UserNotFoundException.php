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
        $errorCode = 404;
        $errorTitle = 'User not found';
        $errorMessage = "The user with ID {$userId} does not exist.";
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
