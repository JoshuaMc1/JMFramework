<?php

namespace Lib\Support;

/**
 * Class Hash
 *
 * Provides hashing and encryption functions.
 */
class Hash
{
    const DEFAULT_ITERATIONS = 1;
    const DEFAULT_ALGORITHM = 'sha256';
    const DEFAULT_HASH_KEY = APP_KEY;

    /**
     * Generate a hashed value for the given string.
     *
     * @param string $value The input value to be hashed.
     * @param array $options An array of options for customizing the hashing process.
     *   - 'algorithm' (string): The hashing algorithm to use (default: sha256).
     *   - 'salt' (string): Additional data to include in the hashing process (default: empty string).
     *   - 'iterations' (int): The number of iterations for the hashing algorithm (default: 1).
     *   - 'hash_key' (string): The key used for hashing (default: value from APP_KEY constant).
     * @return string The hashed value.
     */
    public static function make(string $value, array $options = []): string
    {
        $algorithm = $options['algorithm'] ?? self::DEFAULT_ALGORITHM;
        $salt = $options['salt'] ?? '';
        $iterations = $options['iterations'] ?? self::DEFAULT_ITERATIONS;
        $hashKey = $options['hash_key'] ?? self::DEFAULT_HASH_KEY;

        $hash = hash_hmac($algorithm, $value . $salt, $hashKey);

        for ($i = 1; $i < $iterations; $i++) {
            $hash = hash_hmac($algorithm, $hash . $salt, $hashKey);
        }

        return $hash;
    }

    /**
     * Verify if a given string matches a hashed value.
     *
     * @param string $value The input value to be verified.
     * @param string $hash The hashed value to compare against.
     * @param array $options An array of options for customizing the hashing process (same as make() options).
     * @return bool True if the input value matches the hashed value, false otherwise.
     */
    public static function verify(string $value, string $hash, array $options = []): bool
    {
        return hash_equals(self::make($value, $options), $hash);
    }

    /**
     * Encrypt a string using AES-256-CBC encryption.
     *
     * @param string $value The input value to be encrypted.
     * @return string The encrypted value in base64 encoding.
     */
    public static function encrypt(string $value): string
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
        $encrypted = openssl_encrypt($value, 'AES-256-CBC', self::DEFAULT_HASH_KEY, 0, $iv);
        return base64_encode($encrypted . ':' . $iv);
    }

    /**
     * Decrypt an encrypted string that was previously encrypted using AES-256-CBC encryption.
     *
     * @param string $value The encrypted value in base64 encoding.
     * @return string The decrypted original value.
     */
    public static function decrypt(string $value): string
    {
        $value = base64_decode($value);
        [$encrypted, $iv] = explode(':', $value, 2);
        return openssl_decrypt($encrypted, 'AES-256-CBC', self::DEFAULT_HASH_KEY, 0, $iv);
    }
}
