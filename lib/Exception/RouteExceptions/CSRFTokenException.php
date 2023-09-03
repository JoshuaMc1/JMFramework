<?php

namespace Lib\Exception\RouteExceptions;

use Lib\Exception\CustomException;

/**
 * Class CSRFTokenException
 * 
 * this exception is thrown when there can be a CSRF token error or something else went wrong
 */
class CSRFTokenException extends CustomException
{
    public function __construct()
    {
        parent::__construct(403, 'CSRF Token Error', 'CSRF token is not valid');
    }
}
