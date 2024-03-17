<?php

namespace Lib\Exception\KernelExceptions;

use Lib\Exception\CustomException;

class RouteFileNotFoundException extends CustomException
{
    public function __construct(string $routeFile = '')
    {
        parent::__construct(0401, lang('exception.route_file_not_found'), lang('exception.route_file_not_found_message', ['routeFile' => $routeFile]));
    }
}
