<?php

namespace Lib\Http;

class Response
{
    protected $statusCode = 200;
    protected $headers = [];
    protected $body;

    public function __construct($body = '', $statusCode = 200, $headers = [])
    {
        $this->body = $body;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function withStatus($status)
    {
        $this->statusCode = $status;
        return $this;
    }

    public function withText($body)
    {
        $this->body = $body;
        return $this;
    }

    public function withHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function send()
    {
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        http_response_code($this->statusCode);

        return $this->body;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getHeader($name)
    {
        return $this->headers[$name];
    }

    public static function json($data = [], $statusCode = 200, $headers = [])
    {
        $body = json_encode($data);
        $headers['Content-Type'] = 'application/json';
        $headers['Accept'] = 'application/json';
        return new static($body, $statusCode, $headers);
    }

    public static function text($data, $statusCode = 200, $headers = [])
    {
        $headers['Content-Type'] = 'text/plain';
        return new static($data, $statusCode, $headers);
    }

    public static function html($data, $statusCode = 200, $headers = [])
    {
        $headers['Content-Type'] = 'text/html';
        return new static($data, $statusCode, $headers);
    }
}
