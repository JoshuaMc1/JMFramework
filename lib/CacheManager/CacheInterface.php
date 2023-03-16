<?php

namespace Lib\CacheManager;

interface CacheInterface
{
    /* Checking if the key exists in the cache. */
    public static function has(string $key): bool;

    /* Setting the key and value in the cache. */
    public static function set(string $key, mixed $value, $ttl = 7200): bool;
    /* Returning the value of the key. */
    public static function get(string $key): mixed;

    /* Deleting the key from the cache. */
    public static function delete(string $key): bool;
    /* Deleting all the keys from the cache. */
    public static function clear(): bool;

    /* Returning an array of values. */
    public static function getMultiple(array $keys): array;
    /* Setting multiple values in the cache. */
    public static function setMultiple(array $values, $ttl = 7200): bool;
    /* Deleting multiple keys from the cache. */
    public static function deleteMultiple(array $keys): bool;
}
