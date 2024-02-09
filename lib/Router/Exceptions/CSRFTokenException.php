<?php

namespace Lib\Router\Exceptions;

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
        parent::__construct(403, lang('csrf_token_error'), lang('csrf_token_error_message'));
    }
}
