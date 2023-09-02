<?php

namespace Lib\Exception\RouteExceptions;

use Lib\Exception\CustomException;

/**
 * Class PageNotFoundException
 * 
 * this exception is thrown when a page is not found
 */
class PageNotFoundException extends CustomException
{
    /**
     * The function constructs an error object with a 404 error code, a title of "Page not found", and
     * a message of "Sorry, we couldn’t find the page you’re looking for."
     */
    public function __construct()
    {
        $errorCode = 404;
        $errorTitle = 'Page not found';
        $errorMessage = "Sorry, we couldn’t find the page you’re looking for.";
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
