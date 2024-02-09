<?php

namespace Lib\Http;

/**
 * Class Cors
 * 
 * This class is responsible for handling Cross-Origin Resource Sharing (CORS) headers.
 * */
class Cors
{
    /**
     * Handle CORS headers.
     * 
     * @return void
     * */
    public static function handleCors()
    {
        // Allow any origin or restrict to configured origins.
        $allowedOrigin = config('cors.allowed_origin') ?? '*';
        $origin = self::getOrigin();

        if (self::isOriginAllowed($origin, $allowedOrigin)) {
            header("Access-Control-Allow-Origin: $origin");
        }

        // Handle preflight requests.
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            self::handlePreflight();
            exit();
        }

        // Set other CORS headers.
        header("Access-Control-Allow-Methods: " . implode(', ', config('cors.allowed_methods')));
        header("Access-Control-Allow-Headers: " . implode(', ', config('cors.allowed_headers')));
        header("Access-Control-Expose-Headers: " . implode(', ', config('cors.exposed_headers')));
        header("Access-Control-Max-Age: " . config('cors.max_age'));

        // Allow credentials if configured.
        if (config('cors.supports_credentials')) {
            header("Access-Control-Allow-Credentials: true");
        }
    }

    /**
     * Handle preflight requests.
     * 
     * @return void
     * */
    private static function handlePreflight()
    {
        header("Access-Control-Allow-Methods: " . implode(', ', config('cors.allowed_methods')));
        header("Access-Control-Allow-Headers: " . implode(', ', config('cors.allowed_headers')));
        header("Access-Control-Max-Age: " . config('cors.max_age'));

        // Allow credentials if configured.
        if (config('cors.supports_credentials')) {
            header("Access-Control-Allow-Credentials: true");
        }

        exit();
    }

    /**
     * Get the origin of the request.
     * 
     * @return string|null
     * */
    private static function getOrigin(): ?string
    {
        return isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;
    }

    /**
     * Check if the origin is allowed.
     * 
     * @param string $origin
     * @param string|array $allowedOrigin
     * 
     * @return bool
     * */
    private static function isOriginAllowed($origin, $allowedOrigin): bool
    {
        if ($allowedOrigin === '*' || in_array($origin, $allowedOrigin)) {
            return true;
        }

        foreach ($allowedOrigin as $pattern) {
            if (preg_match("#^$pattern$#", $origin)) {
                return true;
            }
        }

        return false;
    }
}
