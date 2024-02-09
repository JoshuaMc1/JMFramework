<?php

namespace Lib\Exception\ConfigurationExceptions;

use Lib\Exception\CustomException;

class ConfigurationFileNotFoundException extends CustomException
{
    public function __construct(string $configurationFile = '')
    {
        parent::__construct(0701, lang('configuration_file_not_found'), lang('configuration_file_not_found_message', ['configurationFile' => $configurationFile]));
    }
}
