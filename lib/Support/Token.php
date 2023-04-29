<?php

namespace Lib\Support;

use Illuminate\Support\Str;

class Token
{
    protected const DEFAULT_ALGORITHM = 'sha256';
    protected const DEFAULT_EXPIRY = EXPIRY_TOKEN;
    protected const DEFAULT_LENGTH = TOKEN_LENGTH;

    protected static $key = HASH_KEY;

    public static function createToken(array $payload, int $expiry = null, string $algorithm = null): string
    {
        $expiry = $expiry ?? self::DEFAULT_EXPIRY;
        $algorithm = $algorithm ?? self::DEFAULT_ALGORITHM;
        $header = ['alg' => $algorithm];
        $timestamp = time();
        $encodedHeader = base64_encode(json_encode($header));
        $encodedPayload = base64_encode(json_encode($payload));
        $signature = hash_hmac($algorithm, "$encodedHeader.$encodedPayload.$timestamp.$expiry", self::$key);
        return "$encodedHeader.$encodedPayload.$timestamp.$expiry.$signature";
    }

    public static function createBase64Token(int $length = self::DEFAULT_LENGTH)
    {
        return base64_encode(Str::random($length));
    }

    public static function createBinaryToken(int $length = self::DEFAULT_LENGTH)
    {
        return;
        return hex2bin(Str::random($length));
    }

    public static function createPlainTextToken(int $length = self::DEFAULT_LENGTH)
    {
        return Str::uuid() . "|" . Str::random($length);
    }

    public static function decodeToken(string $token): array
    {
        [$encodedHeader, $encodedPayload, $timestamp, $expiry, $signature] = explode('.', $token);
        $header = json_decode(base64_decode($encodedHeader), true);
        $payload = json_decode(base64_decode($encodedPayload), true);
        return [
            'header' => $header,
            'payload' => $payload,
            'timestamp' => $timestamp,
            'expiry' => $expiry,
            'signature' => $signature,
        ];
    }

    public static function verifyToken(string $token, string $algorithm = null): bool
    {
        $algorithm = $algorithm ?? self::DEFAULT_ALGORITHM;
        $decoded = self::decodeToken($token);
        $signature = hash_hmac($algorithm, "{$decoded['header']}.{$decoded['payload']}.{$decoded['timestamp']}.{$decoded['expiry']}", self::$key);
        return hash_equals($decoded['signature'], $signature);
    }
}
