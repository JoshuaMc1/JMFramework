<?php

namespace Lib\Support;

use Exception;
use Lib\Exception\ConfigurationExceptions\ConfigurationFileNotFoundException;
use Lib\Exception\ExceptionHandler;

/**
 * Class Config
 * 
 * Provides functionality for working with configuration files.
 * 
 * @CodeError 07
 */
class Config
{
    /**
     * Get the value of a configuration option.
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     * @throws \Lib\Exception\ConfigurationExceptions\ConfigurationFileNotFoundException
     */
    public static function get(string $key, $default = null)
    {
        try {
            $keys = explode('.', $key);
            $configPath = config_path() . '/' . array_shift($keys) . '.php';

            if (!file_exists($configPath)) {
                throw new ConfigurationFileNotFoundException($configPath);
            }

            $config = include $configPath;

            foreach ($keys as $part) {
                (isset($config[$part])) ? $config = $config[$part] : $config = $default;
            }

            return $config ?? $default;
        } catch (ConfigurationFileNotFoundException | Exception $e) {
            ExceptionHandler::handleException($e);
        }
    }

    /**
     * Set the value of a configuration option.
     * 
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, $value): void
    {
        $_ENV[$key] = $value;
    }
}
