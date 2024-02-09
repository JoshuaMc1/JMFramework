<?php

use Lib\Http\{
    CsrfTokenManager,
    ErrorHandler,
    Request,
    Response,
};
use Lib\Support\{
    Config,
    Date,
    Env,
    Hash,
    Optional,
    View
};
use Lib\Support\Lang\Lang;

if (!function_exists('dd')) {
    /**
     * The `dd` function is a PHP debugging function that outputs the value of a variable along with its
     * data type in a styled HTML format.
     * 
     * @param mixed var The `var` parameter is the variable that you want to dump and die. It can be of any
     * type, such as a boolean, null, integer, float, string, or any other data type. The function will
     * display the type and value of the variable in a formatted output and then terminate
     */
    function dd(mixed $var): void
    {
        $output = "
            <style>
                .dd-wrapper {
                    font-size: 14px;
                    line-height: 1.5;
                    color: #333;
                    font-family: sans-serif;
                    background-color: #D8D8D8;
                    padding: 10px;
                    margin: 10px;
                    border: 1px solid #ccc;
                    border-radius: 4px;
                }

                .dd-header {
                    font-weight: bold;
                    margin-bottom: 10px;
                }

                .dd-type {
                    color: #aaa;
                    margin-right: 10px;
                }

                .dd-str {
                    color: #d14;
                }

                .dd-int,
                .dd-float {
                    color: #4a8;
                }

                .dd-bool {
                    color: #a40;
                }

                .dd-null {
                    color: #9c9c9c;
                }
            </style>
        ";

        $output .= "<div class='dd-wrapper'>";
        $output .= "<div class='dd-header'>Dump and Die</div>";
        $output .= "<pre>";

        switch (gettype($var)) {
            case 'bool':
                $output .= "<span class='dd-type'>bool</span><span class='dd-bool'>" . ($var ? 'true' : 'false') . "</span>";
                break;

            case 'null':
                $output .= "<span class='dd-type'>null</span><span class='dd-null'>null</span>";
                break;

            case 'integer':
                $output .= "<span class='dd-type'>int</span><span class='dd-int'>$var</span>";
                break;

            case 'double':
                $output .= "<span class='dd-type'>float</span><span class='dd-float'>$var</span>";
                break;

            case 'string':
                $output .= "<span class='dd-type'>string(" . strlen($var) . ")</span><span class='dd-str'>$var</span>";
                break;

            default:
                ob_start();
                var_dump($var);
                $output .= ob_get_clean();
                break;
        }

        $output .= "</pre>";
        $output .= "</div>";

        echo $output;
        exit;
    }
}

if (!function_exists('view')) {
    /**
     * The function `view` is a helper function in PHP that renders a view using the Twigs template
     * engine.
     * 
     * @param mixed view The "view" parameter is the name of the view file that you want to render. It
     * should be a string representing the path to the view file relative to the "viewPath" directory.
     * @param array data The `` parameter is an optional array that contains the data that will be
     * passed to the view. This data can be accessed within the view file using variables. For example, if
     * you pass `['name' => 'John']` as the `` parameter, you can access the `
     * 
     * @return string a string.
     */
    function view(mixed $view, array $data = []): string
    {
        return (new View())
            ->render($view, $data);
    }
}

if (!function_exists('asset')) {
    /**
     * The asset function returns the full URL for a given path, taking into account the current server
     * protocol and host.
     * 
     * @param string path The `path` parameter is a string that represents the path to a file or resource.
     * It is optional and defaults to an empty string if not provided.
     * 
     * @return string a string that represents the URL of an asset file.
     */
    function asset(string $path = ''): string
    {
        return ($_SERVER['REQUEST_SCHEME'] ?? 'http') . "://" . $_SERVER['HTTP_HOST'] . "/" . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    /**
     * The function `url` returns the full URL for a given path, using the current server's scheme, host,
     * and a base path.
     * 
     * @param string path The `path` parameter is a string that represents the path to a file or resource
     * within the `/storage` directory. It is optional and defaults to an empty string if not provided.
     * 
     * @return string a string that represents a URL. The URL is constructed using the base URL of the
     * current server, followed by the "/storage/" path, and then the provided  parameter.
     */
    function url(string $path = ''): string
    {
        $base_url = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . $_SERVER['HTTP_HOST'];
        return $base_url . '/storage/' . $path;
    }
}

if (!function_exists('response')) {
    /**
     * The function "response" creates and returns a response object with the specified body, status code,
     * and headers.
     * 
     * @param mixed body The body parameter is the content that will be sent in the response. It can be any
     * type of data, such as a string, an array, or an object. If no value is provided, the body will be
     * set to null by default.
     * @param int status The status parameter is an integer that represents the HTTP status code of the
     * response. It defaults to 200, which means "OK". Other common status codes include 404 for "Not
     * Found", 500 for "Internal Server Error", and 302 for "Found" (used for redirects).
     * @param array headers The `` parameter is an array that contains the HTTP headers to be
     * included in the response. Each element in the array represents a single header, where the key is the
     * header name and the value is the header value.
     * 
     * @return Response an instance of the Response class.
     */
    function response(mixed $body = null, int $status = 200, array $headers = []): Response
    {
        try {
            $response = new Response();
            $response->withText($body)
                ->withStatus($status);

            foreach ($headers as $name => $value) {
                $response->withHeader($name, $value);
            }

            return $response;
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }
}

if (!function_exists('redirect')) {
    /**
     * The function redirects the user to a specified URL with optional HTTP status code and headers.
     * 
     * @param string url The URL to which the user will be redirected.
     * @param int status The status parameter is an optional parameter that specifies the HTTP status code
     * to be sent with the redirect response. The default value is 302, which represents a temporary
     * redirect. Other commonly used status codes for redirects include 301 (permanent redirect) and 307
     * (temporary redirect).
     * @param array headers The `` parameter is an optional array that allows you to specify
     * additional HTTP headers to be sent along with the redirect response. Each element in the array
     * represents a header name-value pair, where the key is the header name and the value is the header
     * value.
     */
    function redirect(string $url, int $status = 302, array $headers = []): void
    {
        try {
            foreach ($headers as $name => $value) {
                header("$name: $value");
            }

            http_response_code($status);
            header("Location: $url");
            exit;
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }
}

if (!function_exists('token')) {
    /**
     * The Token function returns the CSRF token for the current request.
     *  
     * @return string a string
     * */
    function token(): string
    {
        return CsrfTokenManager::getToken();
    }
}

if (!function_exists('csrf')) {
    /**
     * The csrf_token function generates a CSRF token.
     * 
     * @return string a string
     */
    function csrf(): string
    {
        return CsrfTokenManager::csrf();
    }
}

if (!function_exists('isJsonRequest')) {
    /**
     * The function checks if the request is for JSON data.
     * 
     * @return bool The function isJsonRequest() returns a boolean value.
     */
    function isJsonRequest(): bool
    {
        $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? '';
        return strpos($acceptHeader, 'application/json') !== false;
    }
}

if (!function_exists('isAjaxRequest')) {
    /**
     * The function checks if the request is for AJAX data.
     * 
     * @return bool The function isAjaxRequest() returns a boolean value.
     * */
    function isAjaxRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}

if (!function_exists('now')) {
    /**
     * Returns the current date and time in the format "Y-m-d H:i:s".
     * 
     * @return DateTime A DateTime object representing the current date and time in
     * the format 'Y-m-d H:i:s'.
     */
    function now(): DateTime
    {
        return Date::now();
    }
}

if (!function_exists('config_path')) {
    /**
     * Returns the path to the configuration directory.
     * 
     * @return string The path to the configuration directory.
     * */
    function config_path(): string
    {
        return base_path() . '/config';
    }
}

if (!function_exists('storage_path')) {
    /**
     * Returns the path to the storage directory.
     * 
     * @return string The path to the storage directory.
     * */
    function storage_path(): string
    {
        return base_path() . '/storage';
    }
}

if (!function_exists('app_path')) {
    /**
     * Returns the path to the application directory.
     * 
     * @return string The path to the application directory.
     * */
    function app_path(): string
    {
        return base_path() . '/app';
    }
}

if (!function_exists('http_path')) {
    /**
     * Returns the path to the HTTP directory.
     * 
     * @return string The path to the HTTP directory.
     * */
    function http_path(): string
    {
        return app_path() . '/Http';
    }
}

if (!function_exists('controller_path')) {
    /**
     * Returns the path to the controllers directory.
     * 
     * @return string The path to the controllers directory.
     * */
    function controller_path(): string
    {
        return http_path() . '/Controllers';
    }
}

if (!function_exists('middleware_path')) {
    /**
     * Returns the path to the middleware directory.
     * 
     * @return string The path to the middleware directory.
     * */
    function middleware_path(): string
    {
        return http_path() . '/Middleware';
    }
}

if (!function_exists('model_path')) {
    /**
     * Returns the path to the models directory.
     * 
     * @return string The path to the models directory.
     * */
    function model_path(): string
    {
        return app_path() . '/Models';
    }
}

if (!function_exists('base_path')) {
    /**
     * Returns the path to the base directory.
     * 
     * @return string The path to the base directory.
     * */
    function base_path(int $levels = 2): string
    {
        return dirname(__DIR__, $levels);
    }
}

if (!function_exists('public_path')) {
    /**
     * Returns the path to the public directory.
     * 
     * @return string The path to the public directory.
     * */
    function public_path(): string
    {
        return base_path() . '/public';
    }
}

if (!function_exists('asset_path')) {
    /**
     * Returns the path to the public directory.
     * 
     * @return string The path to the public directory.
     * */
    function asset_path(): string
    {
        return base_path() . '/public';
    }
}

if (!function_exists('view_path')) {
    /**
     * Returns the path to the views directory.
     * 
     * @return string The path to the views directory.
     * */
    function view_path(): string
    {
        return base_path() . '/resources/views/';
    }
}

if (!function_exists('components_path')) {
    /**
     * Returns the path to the components directory.
     * 
     * @return string The path to the components directory.
     * */
    function components_path(): string
    {
        return view_path() . '/components';
    }
}

if (!function_exists('lang_path')) {
    /**
     * Returns the path to the language directory.
     * 
     * @return string The path to the language directory.
     * */
    function lang_path(): string
    {
        return base_path() . '/lang';
    }
}

if (!function_exists('routes_path')) {
    /**
     * Returns the path to the routes directory.
     * 
     * @return string The path to the routes directory.
     * */
    function routes_path(): string
    {
        return base_path() . '/routes';
    }
}

if (!function_exists('framework_path')) {
    /**
     * Returns the path to the framework directory.
     * 
     * @return string The path to the framework directory.
     * */
    function framework_path(): string
    {
        return storage_path() . '/framework';
    }
}

if (!function_exists('cache_path')) {
    /**
     * Returns the path to the cache directory.
     * 
     * @return string The path to the cache directory.
     * */
    function cache_path(): string
    {
        return framework_path() . '/.cache';
    }
}

if (!function_exists('session_path')) {
    /**
     * Returns the path to the session directory.
     * 
     * @return string The path to the session directory.
     * */
    function session_path(): string
    {
        return framework_path() . '/sessions';
    }
}

if (!function_exists('log_path')) {
    /**
     * Returns the path to the log directory.
     * 
     * @return string The path to the log directory.
     * */
    function log_path(): string
    {
        return storage_path() . '/logs';
    }
}

if (!function_exists('lib_path')) {
    /**
     * Returns the path to the lib directory.
     * 
     * @return string The path to the lib directory.
     * */
    function lib_path(): string
    {
        return base_path() . '/lib';
    }
}

if (!function_exists('database_path')) {
    /**
     * Returns the path to the database directory.
     * 
     * @return string The path to the database directory.
     */
    function database_path(): string
    {
        return base_path() . '/database';
    }
}

if (!function_exists('config')) {
    /**
     * Returns the configuration value for the given key.
     * 
     * @param string $key The key of the configuration value to retrieve.
     * @return mixed The value of the configuration value.
     */
    function config(string $key, mixed $default = null): mixed
    {
        return Config::get($key, $default);
    }
}

if (!function_exists('env')) {
    /**
     * Returns the value of an environment variable.
     * 
     * @param string $key The key of the environment variable to retrieve.
     * @param mixed $default The default value to return if the environment variable is not set.
     * @return mixed The value of the environment variable.
     */
    function env(string $key, $default = null): mixed
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('app')) {
    /**
     * Returns the configuration value for the given key.
     * 
     * @param string $key The key of the configuration value to retrieve.
     * @param mixed $default The default value to return if the configuration value is not set.
     * @return mixed The value of the configuration value.
     * */
    function app(string $key = null, $default = null)
    {
        return config('app.' . $key, $default);
    }
}

if (!function_exists('optional')) {
    /**
     * Returns the configuration value for the given key.
     * 
     * @param string $key The key of the configuration value to retrieve.
     * @param mixed $default The default value to return if the configuration value is not set.
     * @return mixed The value of the configuration value.
     * */
    function optional(mixed $object = null): Optional
    {
        return new Optional($object);
    }
}

if (!function_exists('request')) {
    /**
     * Returns the request object.
     * 
     * @return Request The request object.
     * */
    function request(): Request
    {
        return new Request();
    }
}

if (!function_exists('route')) {
    /**
     * Get the URI for a named route, replacing specified parameters.
     * 
     * @param string|null $name    The name of the route.
     * @param mixed       $default The default value to return if the route is not found.
     * @param array       $params  An associative array of parameters to replace in the route.
     * 
     * @return string|null The URI of the named route with replaced parameters, or null if not found.
     * */
    function route($name, $default = null, $parameters = []): ?string
    {
        return request()
            ->route($name, $default, $parameters);
    }
}

if (!function_exists('method')) {
    /**
     * Get the HTML form method input.
     * 
     * @param string $method The method to use. Defaults to 'PUT'.
     * 
     * @return string The HTML form method input.
     * */
    function method(string $method): string
    {
        return '<input type="hidden" name="_method" value="' . strtoupper($method) . '">';
    }
}

if (!function_exists('encrypt')) {
    /**
     * Encrypt the given value.
     * 
     * @param mixed $value The value to encrypt.
     * 
     * @return string The encrypted value.
     * */
    function encrypt(mixed $value): string
    {
        return Hash::encrypt($value);
    }
}

if (!function_exists('decrypt')) {
    /**
     * Decrypt the given value.
     * 
     * @param string $value The value to decrypt.
     * 
     * @return mixed The decrypted value.
     * */
    function decrypt(string $value): mixed
    {
        return Hash::decrypt($value);
    }
}

if (!function_exists('bcrypt')) {
    /**
     * Generate a hashed value for the given string.
     * 
     * @param string $value The input value to be hashed.
     * 
     * @return string The hashed value.
     * */
    function bcrypt(mixed $value): string
    {
        return Hash::bcrypt($value);
    }
}


if (!function_exists('verify_bcrypt')) {
    /**
     * Verify if a given string matches a hashed value.
     * 
     * @param string $value The input value to be verified.
     * @param string $hash The hashed value to compare against.
     * 
     * @return bool True if the input value matches the hashed value, false otherwise.
     * */
    function verify_bcrypt(mixed $value, string $hash): bool
    {
        return Hash::verify_bcrypt($value, $hash);
    }
}

if (!function_exists('lang')) {
    /**
     * Get the translation for the given key.
     * 
     * @param string $key The key of the translation.
     * 
     * @return string The translation for the given key.
     * */
    function lang(string $key, array $replacements = []): string
    {
        return Lang::lang($key, $replacements);
    }
}
