<?php

namespace Lib\Support;

use Illuminate\Support\Str;

class Token
{
    protected const DEFAULT_ALGORITHM = 'sha256';
    protected const DEFAULT_EXPIRY = TOKEN_EXPIRY;
    protected const DEFAULT_LENGTH = TOKEN_LENGTH;

    protected static $key = HASH_KEY;

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

    public static function createBase64Token(int $length = self::DEFAULT_LENGTH)
    {
        return base64_encode(Str::random($length));
    }

    public static function createBinaryToken(int $length = self::DEFAULT_LENGTH)
    {
        return bin2hex(Str::random($length));
    }

    public static function createPlainTextToken(int $length = self::DEFAULT_LENGTH)
    {
        return uniqid() . "|" . Str::random($length);
    }

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
