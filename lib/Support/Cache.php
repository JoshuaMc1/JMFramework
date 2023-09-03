<?php

namespace Lib\Support;

use Lib\Exception\ExceptionHandler;
use Lib\Support\CacheManager\CacheInterface;

/**
 * Class Cache
 *
 * Represents a caching system that implements the CacheInterface.
 */
class Cache implements CacheInterface
{
    private static $ttl = 7200; // Time to live for cache entries (default: 7200 seconds)
    private static $cacheDir = __DIR__ . "/../../storage/framework/.cache/"; // Directory where cache files are stored

    /**
     * Check if a cache entry with the given key exists and is still valid.
     *
     * @param string $key The cache entry key.
     * @return bool True if the cache entry exists and is valid, false otherwise.
     */
    public static function has(string $key): bool
    {
        $file = self::getFilePath($key);
        return file_exists($file) && (filemtime($file) + self::$ttl) > time();
    }

    /**
     * Set a cache entry with the given key and value.
     *
     * @param string $key The cache entry key.
     * @param mixed $value The value to be stored in the cache.
     * @param int $ttl The time to live for the cache entry (default: 7200 seconds).
     * @return bool True if the cache entry was successfully set, false otherwise.
     */
    public static function set(string $key, mixed $value, $ttl = 7200): bool
    {
        $file = self::getFilePath($key);
        $directory = dirname($file);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        return file_put_contents($file, serialize($value)) !== false;
    }

    /**
     * Get the value of a cache entry with the given key.
     *
     * @param string $key The cache entry key.
     * @return mixed|null The cached value or null if the cache entry does not exist or is expired.
     */
    public static function get(string $key): mixed
    {
        $file = self::getFilePath($key);

        if (self::has($key)) {
            return unserialize(file_get_contents($file));
        }

        return null;
    }

    /**
     * Delete a cache entry with the given key.
     *
     * @param string $key The cache entry key to be deleted.
     * @return bool True if the cache entry was successfully deleted, false otherwise.
     */
    public static function delete(string $key): bool
    {
        $file = self::getFilePath($key);

        if (file_exists($file)) {
            return unlink($file);
        }

        return false;
    }

    /**
     * Clear all cache entries.
     *
     * @return bool True if all cache entries were successfully cleared, false otherwise.
     */
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

    /**
     * Get values for multiple cache entries.
     *
     * @param array $keys An array of cache entry keys.
     * @return array An associative array of cache entry keys and their corresponding values.
     */
    public static function getMultiple(array $keys): array
    {
        $values = [];

        foreach ($keys as $key) {
            $values[$key] = self::get($key);
        }

        return $values;
    }

    /**
     * Set multiple cache entries with the given values.
     *
     * @param array $values An associative array of cache entry keys and their corresponding values.
     * @param int $ttl The time to live for the cache entries (default: 7200 seconds).
     * @return bool True if all cache entries were successfully set, false otherwise.
     */
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

    /**
     * Delete multiple cache entries with the given keys.
     *
     * @param array $keys An array of cache entry keys to be deleted.
     * @return bool True if all cache entries were successfully deleted, false otherwise.
     */
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

    /**
     * Clear expired cache entries.
     */
    public static function clearExpired()
    {
        $now = time();

        foreach (glob(self::$cacheDir . "*") as $file) {
            if (filemtime($file) + self::$ttl < $now) {
                unlink($file);
            }
        }
    }

    /**
     * Get the cache directory path.
     *
     * @return string The path to the cache directory.
     */
    public static function getDir()
    {
        return self::$cacheDir;
    }

    /**
     * Get the file path for a cache entry based on its key.
     *
     * @param string $key The cache entry key.
     * @return string The file path for the cache entry.
     */
    private static function getFilePath(string $key): string
    {
        return self::$cacheDir . md5(self::validateKey($key));
    }

    /**
     * Validate a cache entry key.
     *
     * @param string $key The cache entry key to be validated.
     * @return string The validated cache entry key.
     */
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
