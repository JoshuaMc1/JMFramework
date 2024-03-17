<?php

namespace Lib\Exception\AppExceptions;

use Lib\Exception\CustomException;

class KeyNotFoundException extends CustomException
{
    public function __construct()
    {
        parent::__construct(1800, lang('exception.key_not_found'), lang('exception.key_not_found_message'));
    }
}
