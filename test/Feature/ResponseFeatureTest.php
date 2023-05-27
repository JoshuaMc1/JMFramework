<?php

use PHPUnit\Framework\TestCase;
use Lib\Http\Response;

class ResponseFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        require_once __DIR__ . '/../../lib/Global/Global.php';
    }

    public function testResponse()
    {
        $response = new Response();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('', $response->getBody());
        $this->assertEquals('', $response->getHeader('Content-Type'));
    }

    public function testResponseJson()
    {
        $data = ['name' => 'John Doe', 'age' => 42];
        $response = Response::json($data, 200);

        $output = $response->getBody();

        $this->assertEquals('{"name":"John Doe","age":42}', $output);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
    }

    public function testResponseHtml()
    {
        $data = '<h1>Hello, World!</h1>';
        $response = Response::html($data, 200);

        $output = $response->getBody();

        $this->assertEquals($data, $output);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('text/html', $response->getHeader('Content-Type'));
    }

    public function testResponseText()
    {
        $data = 'Hello, World!';
        $response = Response::text($data, 200);

        $output = $response->getBody();

        $this->assertEquals($data, $output);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('text/plain', $response->getHeader('Content-Type'));
    }
}
