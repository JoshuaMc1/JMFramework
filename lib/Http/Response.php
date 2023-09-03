<?php

namespace Lib\Http;

/**
 * Class Response
 * 
 * Provides functionality for sending HTTP responses.
 */
class Response
{
    /**
     * The HTTP status code.
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * An associative array of response headers.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * The response body.
     *
     * @var string
     */
    protected $body;

    /**
     * Constructor: Initializes a new Response instance.
     *
     * @param string $body       The response body.
     * @param int    $statusCode The HTTP status code.
     * @param array  $headers    An associative array of response headers.
     */
    public function __construct($body = '', $statusCode = 200, $headers = [])
    {
        $this->body = $body;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Set the HTTP status code for the response.
     *
     * @param int $status The HTTP status code.
     *
     * @return Response The current Response instance.
     */
    public function withStatus($status)
    {
        $this->statusCode = $status;
        return $this;
    }

    /**
     * Set the response body.
     *
     * @param string $body The response body.
     *
     * @return Response The current Response instance.
     */
    public function withText($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Add a response header.
     *
     * @param string $name  The name of the header.
     * @param string $value The value of the header.
     *
     * @return Response The current Response instance.
     */
    public function withHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Send the response with headers and status code.
     *
     * @return string The response body.
     */
    public function send()
    {
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        http_response_code($this->statusCode);

        return $this->body;
    }

    /**
     * Get the HTTP status code of the response.
     *
     * @return int The HTTP status code.
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Get the response body.
     *
     * @return string The response body.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get the value of a specific response header.
     *
     * @param string $name The name of the header.
     *
     * @return string The value of the header.
     */
    public function getHeader($name)
    {
        return $this->headers[$name];
    }

    /**
     * Create a new JSON response.
     *
     * @param array $data       The data to be encoded as JSON.
     * @param int   $statusCode The HTTP status code.
     * @param array $headers    An associative array of response headers.
     *
     * @return Response A new Response instance with JSON content type.
     */
    public static function json($data = [], $statusCode = 200, $headers = [])
    {
        $body = json_encode($data);
        $headers['Content-Type'] = 'application/json';
        $headers['Accept'] = 'application/json';
        return new static($body, $statusCode, $headers);
    }

    /**
     * Create a new plain text response.
     *
     * @param string $data       The text data.
     * @param int    $statusCode The HTTP status code.
     * @param array  $headers    An associative array of response headers.
     *
     * @return Response A new Response instance with text content type.
     */
    public static function text($data, $statusCode = 200, $headers = [])
    {
        $headers['Content-Type'] = 'text/plain';
        return new static($data, $statusCode, $headers);
    }

    /**
     * Create a new HTML response.
     *
     * @param string $data       The HTML content.
     * @param int    $statusCode The HTTP status code.
     * @param array  $headers    An associative array of response headers.
     *
     * @return Response A new Response instance with HTML content type.
     */
    public static function html($data, $statusCode = 200, $headers = [])
    {
        $headers['Content-Type'] = 'text/html';
        return new static($data, $statusCode, $headers);
    }

    /**
     * The __destruct function in PHP is used to unset or remove the properties of an object.
     */
    public function __destruct()
    {
        unset($this->headers);
        unset($this->body);
        unset($this->statusCode);
    }
}
