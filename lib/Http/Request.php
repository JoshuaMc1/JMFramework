<?php

namespace Lib\Http;

use App\Models\User;
use Lib\Exception\ExceptionHandler;
use Lib\Model\Session as SessionModel;
use Lib\Http\Auth;

class Request
{
    /**
     * An associative array of request parameters.
     *
     * @var array
     */
    protected $params = [];

    /**
     * An associative array of request headers.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * The HTTP request method (GET, POST, PUT, DELETE, etc.).
     *
     * @var string
     */
    private $method;

    /**
     * The URI of the request.
     *
     * @var string
     */
    private $uri;

    /**
     * An associative array of request data.
     *
     * @var array
     */
    private $data;

    /**
     * An associative array of uploaded files.
     *
     * @var array
     */
    private $files;

    /**
     * An associative array of registered named routes.
     * 
     * @var array
     * */
    private static $namedRoutes = [];

    /**
     * Constructor: Initializes the request object by populating properties from global variables.
     */
    public function __construct()
    {
        $this->headers = getallheaders();
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->data = $_REQUEST;
        $this->params = $_GET;
        $this->files = $_FILES;
    }

    /**
     * Get the authenticated user associated with the request.
     *
     * @return User|null The authenticated user or null if not authenticated.
     */
    public function user(string $guard = 'web')
    {
        try {
            return Auth::user($guard);
        } catch (\Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    /**
     * Check if the request is authenticated.
     *
     * @return bool|User False if not authenticated, or the authenticated User instance.
     */
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
            ExceptionHandler::handleException($th);
        }
    }

    /**
     * Get a specific header value from the request.
     *
     * @param string $key The name of the header.
     *
     * @return string|null The value of the header or null if not found.
     */
    public function getHeader($key)
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : null;
    }

    /**
     * Get all request headers.
     *
     * @return array An associative array of request headers.
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get the HTTP request method.
     *
     * @return string The HTTP request method.
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get the URI of the request.
     *
     * @return string The URI of the request.
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Get request data by key or the entire data if key is not provided.
     *
     * @param string|null $key The key of the data to retrieve.
     *
     * @return mixed|array|null The data value if key is provided, or the entire data array if key is null.
     */
    public function getData($key = null)
    {
        if ($key === null) {
            return $this->data;
        }

        return $this->data[$key] ?? null;
    }

    /**
     * The function returns the value of the 'Authorization' header or null if it doesn't exist.
     * 
     * @return ?string a string value if the 'Authorization' header is set, otherwise it returns null.
     */
    public function getToken(): ?string
    {
        $token = $this->getHeader('Authorization');

        if (!$token) {
            return null;
        }

        return str_replace('Bearer ', '', $token);
    }

    /**
     * Get all uploaded files.
     *
     * @return array An associative array of uploaded files.
     */
    public function files()
    {
        return $this->files;
    }

    /**
     * Get a specific uploaded file by key.
     *
     * @param string $key The key of the uploaded file.
     *
     * @return mixed|null The uploaded file data or null if not found.
     */
    public function file($key)
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Check if the request method matches the given method.
     *
     * @param string $method The method to check against (GET, POST, PUT, DELETE, etc.).
     *
     * @return bool True if the request method matches, otherwise false.
     */
    public function isMethod($method)
    {
        return $_SERVER['REQUEST_METHOD'] === strtoupper($method);
    }

    /**
     * Get the path from the request URI.
     *
     * @return string The path from the request URI.
     */
    public function getPath()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * Get all request parameters.
     *
     * @return array An associative array of request parameters.
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get a specific request parameter by key.
     *
     * @param string $key The key of the parameter.
     *
     * @return mixed|null The parameter value or null if not found.
     */
    public function getParam($key)
    {
        return $this->params[$key] ?? null;
    }

    /**
     * Get all request data.
     *
     * @return array An associative array of request data.
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Create a collection from the request data.
     *
     * @return \Illuminate\Support\Collection A collection instance containing the request data.
     */
    public function collect()
    {
        return collect($this->data);
    }

    /**
     * Get input data by key, or the entire data if key is not provided.
     *
     * @param string|null $key     The key of the input data to retrieve.
     * @param mixed|null  $default The default value to return if the key is not found.
     *
     * @return mixed|array|null The input data value if key is provided, or the entire input data array if key is null.
     */
    public function input($key = null, $default = null)
    {
        if ($key === null) {
            return $this->data;
        }

        return $this->data[$key] ?? $default;
    }

    /**
     * Get query parameters.
     *
     * @return array An associative array of query parameters.
     */
    public function query()
    {
        return $this->params;
    }

    /**
     * Get a specific request data value as a string.
     *
     * @param string $key The key of the request data.
     *
     * @return string The request data value as a string.
     */
    public function string($key)
    {
        return (string) ($this->data[$key] ?? '');
    }

    /**
     * Get a specific request data value as a boolean.
     *
     * @param string $key The key of the request data.
     *
     * @return bool The request data value as a boolean.
     */
    public function boolean($key)
    {
        return filter_var($this->data[$key] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get a specific request data value as a date.
     *
     * @param string      $key      The key of the request data.
     * @param string      $format   The format that the value should be in.
     * @param string|null $timezone The timezone for the date.
     *
     * @return \DateTime|null The request data value as a \DateTime instance or null if not valid.
     */
    public function date($key, $format = 'Y-m-d')
    {
        $value = $this->data[$key] ?? null;

        if ($value === null) {
            return null;
        }

        $dateTime = \DateTime::createFromFormat($format, $value);

        if ($dateTime === false) {
            return null;
        }

        if (config('app.timezone') !== null) {
            $dateTime->setTimezone(new \DateTimeZone(config('app.timezone')));
        }

        return $dateTime;
    }

    /**
     * Set the named routes.
     * 
     * @param array $namedRoutes The named routes.
     * 
     * @return void
     * */
    public static function setNamedRoutes($namedRoutes): void
    {
        self::$namedRoutes = $namedRoutes;
    }

    /**
     * Get the named routes.
     * 
     * @return array The named routes.
     * */
    public function getNamedRoutes()
    {
        return self::$namedRoutes;
    }

    /**
     * Get the named route.
     * 
     * @param string $name The name of the route.
     * 
     * @return string The named route.
     * */
    public function getNamedRoute($name)
    {
        return self::$namedRoutes[$name];
    }

    /**
     * Check if the named route matches the current route
     * 
     * @param string $name
     * 
     * @return bool true if routes match
     * */
    public function routeIs(string $name)
    {
        $currentRoute = $this->getUri();

        return (strpos($name, '.*') !== false) ?
            $this->checkPatternedRoute($name, $currentRoute) :
            $this->checkExactRoute($name, $currentRoute);
    }

    /**
     * Check if the named route matches the current route
     * 
     * @param string $patternedName
     * @param string $currentRoute
     * 
     * @return bool true if routes match 
     * */
    private function checkPatternedRoute(string $patternedName, string $currentRoute): bool
    {
        $pattern = rtrim($patternedName, '.*');

        foreach (self::$namedRoutes as $routeName => $routeUri) {
            if (strpos($routeName, $pattern) === 0) {
                $normalizedRoute = $this->normalizeRoute($routeUri);
                $normalizedCurrentRoute = $this->normalizeRoute($currentRoute);

                if ($this->matchRoutes($normalizedCurrentRoute, $normalizedRoute)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if the named route matches the current route
     * 
     * @param string $exactName
     * @param string $currentRoute
     * 
     * @return bool true if routes match
     * */
    private function checkExactRoute(string $exactName, string $currentRoute): bool
    {
        if (array_key_exists($exactName, self::$namedRoutes)) {
            $normalizedRoute = $this->normalizeRoute(self::$namedRoutes[$exactName]);
            $normalizedCurrentRoute = $this->normalizeRoute($currentRoute);

            return $this->matchRoutes($normalizedCurrentRoute, $normalizedRoute);
        }

        return false;
    }

    /**
     * Check if two routes match
     * 
     * @param string $currentRoute
     * @param string $namedRoute
     * 
     * @return bool true if routes match
     * */
    private function matchRoutes(string $currentRoute, string $namedRoute): bool
    {
        $currentParts = explode('/', $currentRoute);
        $namedParts = explode('/', $namedRoute);

        if (count($currentParts) !== count($namedParts)) {
            return false;
        }

        foreach ($currentParts as $key => $part) {
            if ($namedParts[$key] !== '{param}' && $currentParts[$key] !== $namedParts[$key]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Normalize route
     * 
     * @param string $routeUri
     * 
     * @return string normalized route uri 
     * */
    private function normalizeRoute(string $routeUri): string
    {
        return preg_replace('/\/:\w+/', '/{param}', $routeUri);
    }

    /**
     * Get the named route.
     * 
     * @param string $name The name of the route.
     * @param array  $params The route parameters.
     * 
     * @return string The named route.
     * */
    public function route($name = null, $params = [])
    {
        $url = self::$namedRoutes[$name] ?? null;

        if ($url && is_array($params) && !empty($params)) {
            foreach ($params as $key => $value) {
                $url = str_replace(":{$key}", rawurlencode($value), $url);
            }
        }

        return '/' . ltrim($url, '/');
    }

    /**
     * Check if the request is an AJAX request.
     * 
     * @return bool
     * */
    public function isAjax()
    {
        return isset($this->headers['X-Requested-With']) && $this->headers['X-Requested-With'] === 'XMLHttpRequest';
    }

    /**
     * Check if the request is a secure request.
     * 
     * @return bool
     * */
    public function isSecure()
    {
        return isset($this->headers['HTTPS']) && $this->headers['HTTPS'] === 'on';
    }

    /**
     * Get the client IP.
     * 
     * @return string
     * */
    public function getClientIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Get the user agent.
     * 
     * @return string
     * */
    public function getUserAgent()
    {
        return $this->headers['User-Agent'] ?? '';
    }

    /**
     * Get the referer URL.
     * 
     * @return string
     * */
    public function getReferer()
    {
        return $this->headers['Referer'] ?? '';
    }

    /**
     * Get the request method override.
     * 
     * @return string
     * */
    public function getMethodOverride()
    {
        return $this->data['_method'] ?? $this->getHeader('X-HTTP-Method-Override');
    }

    /**
     * Check if a file has been uploaded.
     * 
     * @param string $key
     * 
     * @return bool
     * */
    public function hasFile($key)
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    /**
     * Destructor: Unsets the properties to free up memory.
     */
    public function __destruct()
    {
        unset($this->params);
        unset($this->headers);
        unset($this->method);
        unset($this->uri);
        unset($this->data);
    }
}
