<?php

namespace Lib\Support\Cache;

use Lib\Exception\CacheExceptions\EmptyKeyException;
use Lib\Exception\ExceptionHandler;
use Lib\Support\Cache\Contracts\CacheInterface;
use Lib\Support\File;

/**
 * Class Cache
 *
 * Represents a caching system that implements the CacheInterface.
 * 
 * @CodeError 10
 */
class Cache implements CacheInterface
{
    /**
     * Check if a cache entry with the given key exists and is still valid.
     *
     * @param string $key The cache entry key.
     * @return bool True if the cache entry exists and is valid, false otherwise.
     */
    public static function has(string $key): bool
    {
        $file = self::getFilePath($key);

        return file_exists($file) && (filemtime($file) + config('cache.ttl')) > time();
    }

    /**
     * Set a cache entry with the given key and value.
     *
     * @param string $key The cache entry key.
     * @param mixed $value The value to be stored in the cache.
     * @param int $ttl The time to live for the cache entry (default: 7200 seconds).
     * @return bool True if the cache entry was successfully set, false otherwise.
     */
    public static function set(string $key, mixed $value, int $ttl = null): bool
    {
        $ttl = $ttl ?: config('cache.ttl');

        $file = self::getFilePath($key);
        $directory = dirname($file);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (!is_writable($directory)) {
            return false;
        }

        $value = config('cache.secure') ? encrypt($value) : serialize($value);

        return file_put_contents($file, $value) !== false;
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
            return config('cache.secure') ? decrypt(file_get_contents($file)) : unserialize(file_get_contents($file));
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

        if (is_dir(self::getDir())) {
            $files = File::scandir(self::getDir());

            foreach ($files as $file) {
                if (is_file(self::getDir() . $file)) {
                    if ($file === '.gitignore') {
                        continue;
                    }

                    $success = File::delete(self::getDir() . $file);
                }

                if (is_dir(self::getDir() . $file !== 'views')) {
                    $success = File::deleteDirectory(self::getDir() . $file);
                }
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
            $values[$key] = config('cache.secure') ? decrypt(self::get($key)) : unserialize(self::get($key));
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
    public static function setMultiple(array $values, $ttl = null): bool
    {
        $success = true;

        foreach ($values as $key => $value) {
            $value = config('cache.secure') ? encrypt($value) : serialize($value);

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

        foreach (glob(self::getDir() . "*") as $file) {
            if (filemtime($file) + config('cache.ttl') < $now) {
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
        return config('cache.path') . '/';
    }

    /**
     * Get the file path for a cache entry based on its key.
     *
     * @param string $key The cache entry key.
     * @return string The file path for the cache entry.
     */
    private static function getFilePath(string $key): string
    {
        return self::getDir() . md5(self::validateKey($key));
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
                throw new EmptyKeyException();
            }

            return $key;
        } catch (EmptyKeyException $e) {
            ExceptionHandler::handleException($e);
        }
    }
}
