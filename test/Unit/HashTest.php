<?php

use Lib\Support\Hash;
use PHPUnit\Framework\TestCase;

class HashTest extends TestCase
{
    private $hashValue;
    private $hashOptions;

    protected function setUp(): void
    {
        $this->hashValue = 'secret';
        $this->hashOptions = [
            'algorithm' => 'sha256',
            'salt' => 'salty',
            'iterations' => 2,
            'hash_key' => 'my_secret_key'
        ];
    }

    public function testMake()
    {
        $hash = Hash::make($this->hashValue, $this->hashOptions);
        $this->assertNotEmpty($hash);
        $this->assertIsString($hash);
    }

    public function testVerify()
    {
        $hash = Hash::make($this->hashValue, $this->hashOptions);
        $this->assertTrue(Hash::verify($this->hashValue, $hash, $this->hashOptions));

        $this->assertFalse(Hash::verify('wrong_secret', $hash, $this->hashOptions));
    }
}
