<?php

namespace Lib\Http;

/**
 * Class CsrfTokenManager
 * 
 * This class is responsible for managing CSRF tokens.
 */
class CsrfTokenManager
{
    // CSRF token name
    protected static $tokenName = 'csrf_token';

    /**
     * Get the CSRF token.
     * 
     * @return string
     */
    public static function getToken(): string
    {
        return self::getTokenFromCookie(self::$tokenName) ?? '';
    }

    /**
     * Generate and set the CSRF token.
     * 
     * @return string
     * */
    public static function generateToken(): string
    {
        $token = self::getTokenFromCookie(self::$tokenName);

        if (!$token) {
            $token = self::generateRandomToken();

            self::setTokenCookie(self::$tokenName, $token);
        }

        return $token;
    }

    /**
     * Generate and set the CSRF token and return it as an HTML input.
     * 
     * @return string
     * */
    public static function csrf(): string
    {
        return '<input type="hidden" name="_token" value="' . static::generateToken() . '">';
    }

    /**
     * Generate a random CSRF token.
     * 
     * @return string
     * */
    protected static function generateRandomToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Set the CSRF token cookie.
     * 
     * @param string $token
     * 
     * @return void
     * */
    protected static function setTokenCookie(string $tokenName, string $token): void
    {
        Cookie::set($tokenName, $token, time() + 3600, '/');
    }

    /**
     * Get the CSRF token from the cookie.
     * 
     * @param string $tokenName
     * 
     * @return string|null
     * */
    protected static function getTokenFromCookie(string $tokenName): ?string
    {
        return Cookie::get($tokenName);
    }

    /**
     * Verify the CSRF token.
     * 
     * @param string $submittedToken
     * @param string $storedToken
     * 
     * @return bool
     * */
    public static function verifyToken(string $submittedToken, string $storedToken): bool
    {
        return hash_equals($storedToken, $submittedToken);
    }

    /**
     * Validate the CSRF token.
     * 
     * @param string $submittedToken
     * 
     * @return bool
     * */
    public static function validateCsrfToken(string $submittedToken): bool
    {
        $storedToken = self::getTokenFromCookie(self::$tokenName);

        if (!$storedToken || !self::verifyToken($submittedToken, $storedToken)) {
            return false;
        }

        return true;
    }
}
