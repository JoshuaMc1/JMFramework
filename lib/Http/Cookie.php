<?php

namespace Lib\Http;

class Cookie
{
    /**
     * Set a cookie.
     *
     * @param string $name     The name of the cookie.
     * @param string $value    The value to store in the cookie.
     * @param int    $expire   The expiration time in seconds since the Unix epoch.
     * @param string $path     The path on the server where the cookie will be available.
     * @param string $domain   The domain that the cookie is available to.
     * @param bool   $secure   Indicates if the cookie should only be transmitted over secure connections.
     * @param bool   $httpOnly Whether the cookie should be accessible only through the HTTP protocol.
     *
     * @return void
     */
    public static function set(string $name, string $value, int $expire = 0, string $path = '', string $domain = '', bool $secure = false, bool $httpOnly = false)
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Get the value of a cookie.
     *
     * @param string $name The name of the cookie.
     *
     * @return string|null The value of the cookie, or null if the cookie does not exist.
     */
    public static function get(string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * Remove a cookie by setting its expiration time to the past.
     *
     * @param string $name     The name of the cookie to remove.
     * @param string $path     The path on the server where the cookie was available.
     * @param string $domain   The domain that the cookie was available to.
     * @param bool   $secure   Indicates if the cookie was transmitted over secure connections.
     * @param bool   $httpOnly Whether the cookie was accessible only through the HTTP protocol.
     *
     * @return void
     */
    public static function remove(string $name, string $path = '', string $domain = '', bool $secure = false, bool $httpOnly = false)
    {
        if (isset($_COOKIE[$name])) {
            setcookie($name, '', time() - 3600, $path, $domain, $secure, $httpOnly);
            unset($_COOKIE[$name]);
        }
    }

    /**
     * Check if a cookie exists.
     *
     * @param string $name The name of the cookie.
     *
     * @return bool True if the cookie exists, otherwise false.
     */
    public static function has(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }
}
