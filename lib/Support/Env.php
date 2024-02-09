<?php

namespace Lib\Support;

use Dotenv\Dotenv;
use Lib\Exception\EnvironmentExceptions\EnvironmentFileNotFoundException;
use Lib\Exception\ExceptionHandler;
use Throwable;

/**
 * Class Env
 * 
 * Provides functionality for working with environment variables.
 * 
 * @CodeError 03
 */
class Env
{
    /**
     * Load environment variables from a file.
     *
     * @param string $filePath
     * @return void
     */
    public static function load(string $filePath = null): void
    {
        try {
            $filePath = $filePath ?: base_path() . '/.env';

            if (!file_exists($filePath)) {
                throw new EnvironmentFileNotFoundException($filePath);
            }

            $dotenv = Dotenv::createImmutable(dirname($filePath));
            $dotenv->load();
        } catch (EnvironmentFileNotFoundException | Throwable $e) {
            ExceptionHandler::handleException($e);
        }
    }

    /**
     * Get the value of an environment variable.
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }

    /**
     * Set the value of an environment variable.
     * 
     * @param string $key
     * @param mixed $value
     * 
     * @return void
     * */
    public static function set(string $key, mixed $value = null): void
    {
        $_ENV[$key] = $value;
    }
}
