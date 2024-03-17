<?php

namespace Lib\Exception\CacheExceptions;

use Lib\Exception\CustomException;

class EmptyKeyException extends CustomException
{
    public function __construct()
    {
        parent::__construct(1001, lang('exception.cache_empty_key'), lang('exception.cache_empty_key_message'));
    }
}
