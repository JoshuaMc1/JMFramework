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
    /**
     * Create a Custom JMToken (JMT) with a payload and expiration time.
     *
     * @param array $payload The payload data to be included in the token.
     * @param int $expiry The expiration time of the token in seconds (default: TOKEN_EXPIRY).
     * @param string $algorithm The hashing algorithm to use (default: sha256).
     * @return string The generated JMToken.
     */
    public static function createToken(array $payload, int $expiry = null, string $algorithm = null): string
    {
        $expiry = $expiry ?? config('token.expiry');
        $algorithm = $algorithm ?? config('token.algorithm');
        $header = ['alg' => $algorithm];
        $timestamp = time();
        $data = [
            'payload' => $payload,
            'timestamp' => $timestamp,
            'expiry' => $timestamp + $expiry,
        ];
        $encodedHeader = base64_encode(json_encode($header));
        $encodedPayload = base64_encode(json_encode($data));
        $signature = hash_hmac($algorithm, "$encodedHeader.$encodedPayload", config('app.key'));

        return "$encodedHeader.$encodedPayload.$signature";
    }

    /**
     * Create a base64-encoded token of a specified length.
     *
     * @param int $length The length of the base64-encoded token (default: TOKEN_LENGTH).
     * 
     * @return string The generated base64-encoded token.
     */
    public static function createBase64Token(int $length): string
    {
        $length = $length ?? config('token.length');

        return base64_encode(Str::random($length));
    }

    /**
     * Create a binary token (hexadecimal) of a specified length.
     *
     * @param int $length The length of the binary token (default: TOKEN_LENGTH).
     * 
     * @return string The generated binary token.
     */
    public static function createBinaryToken(int $length): string
    {
        $length = $length ?? config('token.length');

        return bin2hex(Str::random($length));
    }

    /**
     * Create a plain text token consisting of a unique ID and random characters.
     *
     * @param int $length The length of the random character part (default: TOKEN_LENGTH).
     * 
     * @return string The generated plain text token.
     */
    public static function createPlainTextToken(int $length): string
    {
        $length = $length ?? config('token.length');

        return uniqid() . "|" . Str::random($length);
    }

    /**
     * Decode and validate a JMToken and return its payload.
     *
     * @param string $token The JMToken to decode and validate.
     * 
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

        if (hash_hmac($header['alg'], "$encodedHeader.$encodedPayload", config('app.key')) !== $signature) {
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
