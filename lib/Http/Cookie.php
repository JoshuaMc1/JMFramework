<?php

namespace Lib\Http;

class Cookie
{
    public static function set(string $name, string $value, int $expire = 0, string $path = '', string $domain = '', bool $secure = false, bool $httpOnly = false)
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    public static function get(string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    public static function remove(string $name, string $path = '', string $domain = '', bool $secure = false, bool $httpOnly = false)
    {
        if (isset($_COOKIE[$name])) {
            setcookie($name, '', time() - 3600, $path, $domain, $secure, $httpOnly);
            unset($_COOKIE[$name]);
        }
    }

    public static function has(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }
}
