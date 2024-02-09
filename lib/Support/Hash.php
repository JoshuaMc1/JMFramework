<?php

namespace Lib\Support;

/**
 * Class Hash
 *
 * Provides hashing and encryption functions.
 */
class Hash
{
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
        $algorithm = $options['algorithm'] ?? config('hash.algorithm', 'sha256');
        $salt = $options['salt'] ?? '';
        $iterations = $options['iterations'] ?? config('hash.iterations', 1);
        $hashKey = $options['hash_key'] ?? config('app.key');

        $hash = hash_hmac($algorithm, $value . $salt, $hashKey);

        for ($i = 1; $i < $iterations; $i++) {
            $hash = hash_hmac($algorithm, $hash . $salt, $hashKey);
        }

        return $hash;
    }

    /**
     * Verify that the given value matches the given hash.
     * 
     * @param string $value The input value to be verified.
     * @param string $hash The hash to compare against.
     * @param array $options An array of options for customizing the verification process.
     * 
     * @return bool True if the input value matches the hashed value, false otherwise.
     */
    public static function verify(string $value, string $hash, array $options = []): bool
    {
        return hash_equals(self::make($value, $options), $hash);
    }

    /**
     * Encrypt the given value.
     * 
     * @param mixed $data The value to encrypt.
     * 
     * @return string The encrypted value.
     * */
    public static function encrypt(mixed $data): string
    {
        if (!is_string($data)) {
            $data = serialize($data);
        }

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(config('hash.encrypt')));

        $encrypted = openssl_encrypt($data, config('hash.encrypt'), config('app.key'), 0, $iv);

        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt the given value.
     * 
     * @param string $data The value to decrypt.
     * 
     * @return string The decrypted value.
     * */
    public static function decrypt(string $data): mixed
    {
        $data = base64_decode($data);

        $ivSize = openssl_cipher_iv_length(config('hash.encrypt'));
        $iv = substr($data, 0, $ivSize);
        $data = substr($data, $ivSize);

        return unserialize(openssl_decrypt($data, config('hash.encrypt'), config('app.key'), 0, $iv)) ?? null;
    }

    /**
     * Generate a hashed value for the given string.
     * 
     * @param string $value The input value to be hashed.
     * 
     * @return string The hashed value.
     * */
    public static function bcrypt(mixed $value): string
    {
        return password_hash($value, PASSWORD_BCRYPT);
    }

    /**
     * Verify if a given string matches a hashed value.
     * 
     * @param string $value The input value to be verified.
     * 
     * @return bool True if the input value matches the hashed value, false otherwise.
     * */
    public static function verify_bcrypt(mixed $value, string $hash): bool
    {
        return password_verify($value, $hash);
    }
}
