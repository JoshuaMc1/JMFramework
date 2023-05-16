<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Lib\Support\Token;

class TokenTest extends TestCase
{
    private const TEST_EXPIRY = 3600;
    private const TEST_PAYLOAD = [
        'user_id' => 1,
        'name' => 'John Doe',
        'email' => 'john.doe@example.com'
    ];
    private const TEST_ALGORITHM = 'sha256';
    private const TEST_LENGTH = 32;

    public function testCreateToken(): void
    {
        $token = Token::createToken(self::TEST_PAYLOAD, self::TEST_EXPIRY, self::TEST_ALGORITHM);
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
    }

    public function testCreateBase64Token(): void
    {
        $token = Token::createBase64Token(self::TEST_LENGTH);
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\/\+]+={0,2}$/', $token);
    }

    public function testCreateBinaryToken(): void
    {
        $token = Token::createBinaryToken(self::TEST_LENGTH);
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
    }

    public function testCreatePlainTextToken(): void
    {
        $token = Token::createPlainTextToken(self::TEST_LENGTH);
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        $this->assertMatchesRegularExpression('/^[a-f0-9\-]+\|[a-zA-Z0-9]{32}$/', $token);
    }

    public function testDecodeToken(): void
    {
        $token = Token::createToken(self::TEST_PAYLOAD, self::TEST_EXPIRY, self::TEST_ALGORITHM);
        $decoded = Token::decodeToken($token);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('header', $decoded);
        $this->assertArrayHasKey('payload', $decoded);
        $this->assertArrayHasKey('timestamp', $decoded);
        $this->assertArrayHasKey('expiry', $decoded);
        $this->assertArrayHasKey('signature', $decoded);
        $this->assertIsArray($decoded['header']);
        $this->assertIsArray($decoded['payload']);
        $this->assertIsInt($decoded['timestamp']);
        $this->assertIsInt($decoded['expiry']);
        $this->assertIsString($decoded['signature']);
    }

    public function testVerifyToken(): void
    {
        $token = Token::createToken(self::TEST_PAYLOAD, self::TEST_EXPIRY, self::TEST_ALGORITHM);
        $this->assertTrue(Token::verifyToken($token, self::TEST_ALGORITHM));
    }
}
