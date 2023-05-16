<?php

namespace Lib\Http;

use App\Models\User;
use Lib\Model\Session as SessionModel;
use Lib\Support\Token;

class Request
{
    protected $params = [];
    protected $headers = [];
    private $method;
    private $uri;
    private $data;
    private $files;

    public function __construct()
    {
        $this->headers = getallheaders();
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->data = $_REQUEST;
        $this->params = $_GET;
        $this->files = $_FILES;
    }

    public function user()
    {
        try {
            $header = $this->getHeader('Authorization');

            if (!$header) {
                return null;
            }

            $parts = explode(' ', $header);
            if (count($parts) === 2 && $parts[0] === 'Bearer') {
                $token = $parts[1];

                $decoded = Token::decodeToken($token);

                if (!$decoded['status']) {
                    return null;
                }

                $user = User::find($decoded['payload']);

                if (!$user) {
                    return null;
                }

                return $user;
            } else {
                $token = $header;
                $decoded = Token::decodeToken($token);
                if (!$decoded['status']) {
                    return null;
                }

                return $decoded['payload'];
            }
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }

    public function isAuthenticated()
    {
        try {
            $sessionId = Cookie::get('session_id');

            if (!$sessionId) {
                return false;
            }

            $session = SessionModel::find($sessionId);

            if (!$session) {
                return false;
            }

            $user = User::find($session['user_id']);

            if (!$user) {
                return false;
            }

            Session::updateLastActivity($sessionId);

            return $user;
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }

    public function getHeader($key)
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : null;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getData($key = null)
    {
        if ($key === null) {
            return $this->data;
        }
        return $this->data[$key] ?? null;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function getFile($key)
    {
        return $this->files[$key] ?? null;
    }

    public function isMethod($method)
    {
        return $_SERVER['REQUEST_METHOD'] === strtoupper($method);
    }

    public function getPath()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getParam($key)
    {
        return $this->params[$key] ?? null;
    }

    public function all()
    {
        return $this->data;
    }

    public function __destruct()
    {
        unset($this->params);
        unset($this->headers);
        unset($this->method);
        unset($this->uri);
        unset($this->data);
    }
}
