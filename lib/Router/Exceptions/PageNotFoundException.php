<?php

namespace Lib\Router\Exceptions;

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
        $errorTitle = lang('exception.page_not_found');
        $errorMessage = lang('exception.page_not_found_message');
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
