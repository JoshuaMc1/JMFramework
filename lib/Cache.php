<?php

namespace Lib;

use Lib\CacheManager\CacheInterface;

class Cache implements CacheInterface
{
    /* Setting the default time to live for the cache. */
    private static $ttl = 7200;
    /* Setting the default cache directory. */
    private static $cacheDir = __DIR__ . "/../.cache/";

    /**
     * `getFilePath` takes a string as an argument and returns a string
     * 
     * @param string key The key to store the data under.
     * 
     * @return string The file path of the cache file.
     */
    private static function getFilePath(string $key): string
    {
        return self::$cacheDir . md5($key);
    }

    /**
     * > If the file exists and the file's last modified time plus the time to live is greater than the
     * current time, then return true
     * 
     * @param string key The key to use for the cache
     */
    public static function has(string $key): bool
    {
        $file = self::getFilePath($key);
        return file_exists($file) && (filemtime($file) + self::$ttl) > time();
    }

    /**
     * It creates a directory if it doesn't exist, then writes the serialized value to a file
     * 
     * @param string key The key to store the value under.
     * @param mixed value The value to store.
     * @param ttl Time to live in seconds.
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
     * It gets the file path of the key, checks if the key exists, and if it does, it returns the
     * unserialized contents of the file
     * 
     * @param string key The key of the item to store.
     * 
     * @return mixed The value of the key.
     */
    public static function get(string $key): mixed
    {
        $file = self::getFilePath($key);
        if (self::has($key)) {
            return unserialize(file_get_contents($file));
        }
        return false;
    }

    /**
     * > Delete a file from the cache
     * 
     * @param string key The key of the item to store.
     * 
     * @return bool The return value is a boolean.
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
     * It deletes all files in the cache directory.
     * 
     * @return bool a boolean value.
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
     * It takes an array of keys, and returns an array of values
     * 
     * @param array keys An array of keys to retrieve from the cache.
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
     * It loops through an array of values and calls the set function for each value
     * 
     * @param array values An array of key => value pairs to set.
     * @param ttl Time to live in seconds.
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
     * > Delete multiple keys from the cache
     * 
     * @param array keys An array of keys to delete
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
     * It deletes all files in the cache directory that are older than the time to live
     */
    public static function clearExpired()
    {
        foreach (glob(self::$cacheDir . "/*") as $file) {
            if (filemtime($file) + self::$ttl < time()) {
                unlink($file);
            }
        }
    }
}
