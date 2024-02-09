<?php

namespace Lib\Exception\EnvironmentExceptions;

use Lib\Exception\CustomException;

class EnvironmentFileNotFoundException extends CustomException
{
    public function __construct(string $environmentFile = '')
    {
        parent::__construct(0301, lang('environment_file_not_found'), lang('environment_file_not_found_message', ['environmentFile' => $environmentFile]));
    }
}
