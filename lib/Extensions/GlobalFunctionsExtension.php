<?php

namespace Lib\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GlobalFunctionsExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('dd', 'dd'),
            new TwigFunction('view', 'view'),
            new TwigFunction('asset', 'asset'),
            new TwigFunction('url', 'url'),
            new TwigFunction('response', 'response'),
            new TwigFunction('redirect', 'redirect'),
            new TwigFunction('token', 'token'),
            new TwigFunction('csrf', 'csrf'),
            new TwigFunction('isJsonRequest', 'isJsonRequest'),
            new TwigFunction('isAjaxRequest', 'isAjaxRequest'),
            new TwigFunction('now', 'now'),
            new TwigFunction('config_path', 'config_path'),
            new TwigFunction('storage_path', 'storage_path'),
            new TwigFunction('app_path', 'app_path'),
            new TwigFunction('http_path', 'http_path'),
            new TwigFunction('controller_path', 'controller_path'),
            new TwigFunction('middleware_path', 'middleware_path'),
            new TwigFunction('model_path', 'model_path'),
            new TwigFunction('base_path', 'base_path'),
            new TwigFunction('public_path', 'public_path'),
            new TwigFunction('asset_path', 'asset_path'),
            new TwigFunction('view_path', 'view_path'),
            new TwigFunction('components_path', 'components_path'),
            new TwigFunction('lang_path', 'lang_path'),
            new TwigFunction('routes_path', 'routes_path'),
            new TwigFunction('framework_path', 'framework_path'),
            new TwigFunction('cache_path', 'cache_path'),
            new TwigFunction('session_path', 'session_path'),
            new TwigFunction('log_path', 'log_path'),
            new TwigFunction('lib_path', 'lib_path'),
            new TwigFunction('database_path', 'database_path'),
            new TwigFunction('config', 'config'),
            new TwigFunction('env', 'env'),
            new TwigFunction('app', 'app'),
            new TwigFunction('optional', 'optional'),
            new TwigFunction('request', 'request'),
            new TwigFunction('route', 'route'),
            new TwigFunction('method', 'method'),
            new TwigFunction('encrypt', 'encrypt'),
            new TwigFunction('decrypt', 'decrypt'),
            new TwigFunction('bcrypt', 'bcrypt'),
            new TwigFunction('verify_bcrypt', 'verify_bcrypt'),
            new TwigFunction('lang', 'lang'),
            new TwigFunction('auth', 'auth'),
        ];
    }
}
