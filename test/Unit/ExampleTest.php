<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\{
    CoversClass,
    UsesClass
};

#[UsesClass(ExampleTest::class)]
#[CoversClass(ExampleTest::class)]
class ExampleTest extends TestCase
{
    /** @test */
    public function testIsTrue()
    {
        $this->assertTrue(true);
    }
}
