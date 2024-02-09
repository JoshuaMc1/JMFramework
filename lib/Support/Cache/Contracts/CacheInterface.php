<?php

namespace Lib\Support\Cache\Contracts;

/**
 * Interface CacheInterface
 * 
 * Provides methods to interact with the cache.
 */
interface CacheInterface
{
    /**
     * Checking if the key exists in the cache.
     * 
     * @param string $key
     * 
     * @return bool
     */
    public static function has(string $key): bool;

    /**
     * Setting a value in the cache.
     * 
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * 
     * @return bool
     */
    public static function set(string $key, mixed $value, int $ttl): bool;

    /**
     * Getting a value from the cache.
     * 
     * @param string $key
     * 
     * @return mixed
     */
    public static function get(string $key): mixed;

    /**
     * Deleting a key from the cache.
     * 
     * @param string $key
     * 
     * @return bool
     */
    public static function delete(string $key): bool;

    /**
     * Clearing the entire cache.
     * 
     * @return bool
     */
    public static function clear(): bool;

    /**
     * Getting multiple values from the cache.
     * 
     * @param array $keys
     * 
     * @return array
     */
    public static function getMultiple(array $keys): array;

    /**
     * Setting multiple values in the cache.
     * 
     * @param array $values
     * @param int $ttl
     * 
     * @return bool
     */
    public static function setMultiple(array $values, int $ttl): bool;

    /**
     * Deleting multiple keys from the cache.
     * 
     * @param array $keys
     * 
     * @return bool
     */
    public static function deleteMultiple(array $keys): bool;
}
