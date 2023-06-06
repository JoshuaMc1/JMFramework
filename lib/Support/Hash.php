<?php

namespace Lib\Support;

class Hash
{
    const DEFAULT_ITERATIONS = 1;
    const DEFAULT_ALGORITHM = 'sha256';
    const DEFAULT_HASH_KEY = APP_KEY;

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

    public static function verify(string $value, string $hash, array $options = []): bool
    {
        return hash_equals(self::make($value, $options), $hash);
    }

    public static function encrypt(string $value): string
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
        $encrypted = openssl_encrypt($value, 'AES-256-CBC', self::DEFAULT_HASH_KEY, 0, $iv);
        return base64_encode($encrypted . ':' . $iv);
    }

    public static function decrypt(string $value): string
    {
        $value = base64_decode($value);
        [$encrypted, $iv] = explode(':', $value, 2);
        return openssl_decrypt($encrypted, 'AES-256-CBC', self::DEFAULT_HASH_KEY, 0, $iv);
    }
}
