<?php

namespace Lib\Support;

use Lib\Exception\ExceptionHandler;
use Lib\Support\CacheManager\CacheInterface;

class Cache implements CacheInterface
{
    private static $ttl = 7200;
    private static $cacheDir = __DIR__ . "/../../storage/.cache/";

    public static function has(string $key): bool
    {
        $file = self::getFilePath($key);
        return file_exists($file) && (filemtime($file) + self::$ttl) > time();
    }

    public static function set(string $key, mixed $value, $ttl = 7200): bool
    {
        $file = self::getFilePath($key);
        $directory = dirname($file);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        return file_put_contents($file, serialize($value)) !== false;
    }

    public static function get(string $key): mixed
    {
        $file = self::getFilePath($key);

        if (self::has($key)) {
            return unserialize(file_get_contents($file));
        }

        return null;
    }

    public static function delete(string $key): bool
    {
        $file = self::getFilePath($key);

        if (file_exists($file)) {
            return unlink($file);
        }

        return false;
    }

    public static function clear(): bool
    {
        $success = true;

        foreach (glob(self::$cacheDir . "*") as $file) {
            if (!unlink($file)) {
                $success = false;
            }
        }

        return $success;
    }

    public static function getMultiple(array $keys): array
    {
        $values = [];

        foreach ($keys as $key) {
            $values[$key] = self::get($key);
        }

        return $values;
    }

    public static function setMultiple(array $values, $ttl = 7200): bool
    {
        $success = true;

        foreach ($values as $key => $value) {
            if (!self::set($key, $value, $ttl)) {
                $success = false;
            }
        }

        return $success;
    }

    public static function deleteMultiple(array $keys): bool
    {
        $success = true;

        foreach ($keys as $key) {
            if (!self::delete($key)) {
                $success = false;
            }
        }

        return $success;
    }

    public static function clearExpired()
    {
        $now = time();

        foreach (glob(self::$cacheDir . "*") as $file) {
            if (filemtime($file) + self::$ttl < $now) {
                unlink($file);
            }
        }
    }

    public static function getDir()
    {
        return self::$cacheDir;
    }

    private static function getFilePath(string $key): string
    {
        return self::$cacheDir . md5(self::validateKey($key));
    }

    private static function validateKey(string $key): string
    {
        try {
            $key = trim($key);

            if ($key === '') {
                throw new \Exception('Cache key cannot be empty.', 404);
            }

            return $key;
        } catch (\Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }
}
