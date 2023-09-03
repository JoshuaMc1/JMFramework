<?php

namespace Lib\Support;

use Illuminate\Support\Str;

/**
 * Class Token
 *
 * Provides token creation and validation functions.
 */
class Token
{
    protected const DEFAULT_ALGORITHM = 'sha256';
    protected const DEFAULT_EXPIRY = TOKEN_EXPIRY;
    protected const DEFAULT_LENGTH = TOKEN_LENGTH;

    protected static $key = APP_KEY;

    /**
     * Create a JSON Web Token (JWT) with the given payload.
     *
     * @param array $payload The payload data to be included in the token.
     * @param int $expiry The expiration time of the token in seconds (default: TOKEN_EXPIRY).
     * @param string $algorithm The hashing algorithm to use (default: sha256).
     * @return string The generated JWT.
     */
    public static function createToken(array $payload, int $expiry = null, string $algorithm = null): string
    {
        $expiry = $expiry ?? self::DEFAULT_EXPIRY;
        $algorithm = $algorithm ?? self::DEFAULT_ALGORITHM;
        $header = ['alg' => $algorithm];
        $timestamp = time();
        $data = [
            'payload' => $payload,
            'timestamp' => $timestamp,
            'expiry' => $timestamp + $expiry,
        ];
        $encodedHeader = base64_encode(json_encode($header));
        $encodedPayload = base64_encode(json_encode($data));
        $signature = hash_hmac($algorithm, "$encodedHeader.$encodedPayload", self::$key);
        return "$encodedHeader.$encodedPayload.$signature";
    }

    /**
     * Create a base64-encoded token of a specified length.
     *
     * @param int $length The length of the base64-encoded token (default: TOKEN_LENGTH).
     * @return string The generated base64-encoded token.
     */
    public static function createBase64Token(int $length = self::DEFAULT_LENGTH)
    {
        return base64_encode(Str::random($length));
    }

    /**
     * Create a binary token (hexadecimal) of a specified length.
     *
     * @param int $length The length of the binary token (default: TOKEN_LENGTH).
     * @return string The generated binary token.
     */
    public static function createBinaryToken(int $length = self::DEFAULT_LENGTH)
    {
        return bin2hex(Str::random($length));
    }

    /**
     * Create a plain text token consisting of a unique ID and random characters.
     *
     * @param int $length The length of the random character part (default: TOKEN_LENGTH).
     * @return string The generated plain text token.
     */
    public static function createPlainTextToken(int $length = self::DEFAULT_LENGTH)
    {
        return uniqid() . "|" . Str::random($length);
    }

    /**
     * Decode and validate a JSON Web Token (JWT).
     *
     * @param string $token The JWT to decode and validate.
     * @return array An array containing the decoded token data if valid, or an error message if invalid.
     */
    public static function decodeToken(string $token): array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return [
                'status' => false,
                'message' => 'Invalid token format',
            ];
        }

        [$encodedHeader, $encodedPayload, $signature] = $parts;

        $header = json_decode(base64_decode($encodedHeader), true);
        $data = json_decode(base64_decode($encodedPayload), true);

        if (empty($header) || empty($data) || empty($data['timestamp']) || empty($data['expiry']) || empty($signature)) {
            return [
                'status' => false,
                'message' => 'Invalid token format',
            ];
        }

        if (hash_hmac($header['alg'], "$encodedHeader.$encodedPayload", self::$key) !== $signature) {
            return [
                'status' => false,
                'message' => 'Invalid token signature',
            ];
        }

        $now = time();

        if ($data['expiry'] <= $now) {
            return [
                'status' => false,
                'message' => 'Token has expired',
            ];
        }

        return [
            'status' => true,
            'payload' => $data['payload'],
            'timestamp' => $data['timestamp'],
            'expiry' => $data['expiry'],
        ];
    }
}
