<?php

use Lib\Http\{Cookie, ErrorHandler, Response};
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\{CompilerEngine, EngineResolver};
use Illuminate\View\{Factory, FileViewFinder};

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
    $output = "<style>
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
        </style>";

    $output .= "<div class='dd-wrapper'>";
    $output .= "<div class='dd-header'>Dump and Die</div>";
    $output .= "<pre>";

    if (is_bool($var)) {
        $output .= "<span class='dd-type'>bool</span><span class='dd-bool'>" . ($var ? 'true' : 'false') . "</span>";
    } elseif (is_null($var)) {
        $output .= "<span class='dd-type'>null</span><span class='dd-null'>null</span>";
    } elseif (is_int($var)) {
        $output .= "<span class='dd-type'>int</span><span class='dd-int'>$var</span>";
    } elseif (is_float($var)) {
        $output .= "<span class='dd-type'>float</span><span class='dd-float'>$var</span>";
    } elseif (is_string($var)) {
        $output .= "<span class='dd-type'>string(" . strlen($var) . ")</span><span class='dd-str'>$var</span>";
    } else {
        ob_start();
        var_dump($var);
        $output .= ob_get_clean();
    }

    $output .= "</pre>";
    $output .= "</div>";

    echo $output;
    exit;
}

/**
 * The function `view` is a helper function in PHP that renders a view using the Blade templating
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
    try {
        $viewPath = __DIR__ . '/../../resources/views/';
        $cachePath = __DIR__ . '/../../storage/framework/.cache/views';

        $filesystem = new Filesystem();
        $viewFinder = new FileViewFinder($filesystem, [$viewPath]);
        $eventDispatcher = new Dispatcher(new Container());

        $resolver = new EngineResolver();
        $compiler = new BladeCompiler($filesystem, $cachePath);

        $resolver->register('blade', function () use ($compiler) {
            return new CompilerEngine($compiler);
        });

        $factory = new Factory($resolver, $viewFinder, $eventDispatcher);
        $factory->addExtension('blade', 'blade');

        return $factory->make($view, $data)->render();
    } catch (\Throwable $th) {
        ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
    }
}

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

/**
 * The function is_valid_csrf_token returns a boolean value.
 * 
 * @return bool The function is_valid_csrf_token returns a boolean value.
 */
function is_valid_csrf_token(): bool
{
    return isset($_POST['_token']) && $_POST['_token'] === Cookie::get('csrf_token');
}

/**
 * The csrf_token function generates a CSRF token.
 *  
 * @return string a string
 * */
function csrf_token(): string
{
    return bin2hex(random_bytes(32));
}

/**
 * The csrf_token function generates a CSRF token.
 * 
 * @return string a string
 */
function csrf(): string
{
    return '<input type="hidden" name="_token" value="' . Cookie::get('csrf_token') . '">';
}

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

/**
 * Returns the current date and time in the format "Y-m-d H:i:s".
 * 
 * @return string The function `now()` returns a string representation of the current date and time in
 * the format 'Y-m-d H:i:s'.
 */
function now(): string
{
    return date('Y-m-d H:i:s');
}
