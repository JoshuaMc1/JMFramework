<?php

namespace Lib\Exception\RouteExceptions;

use Lib\Exception\CustomException;

class PageNotFoundException extends CustomException
{
    public function __construct()
    {
        $errorCode = 404;
        $errorTitle = 'Page not found';
        $errorMessage = "Sorry, we couldn’t find the page you’re looking for.";
        parent::__construct($errorCode, $errorTitle, $errorMessage);
    }
}
