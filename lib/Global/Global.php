<?php

namespace Lib\Global;

use Lib\Http\ErrorHandler;
use Lib\Http\Response;

function dd($var)
{
    echo "<style>
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
        .dd-int {
        color: #4a8;
        }
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

    echo "<div class='dd-wrapper'>";
    echo "<div class='dd-header'>Dump and Die</div>";
    echo "<pre>";

    if (is_bool($var)) {
        echo "<span class='dd-type'>bool</span><span class='dd-bool'>" . ($var ? 'true' : 'false') . "</span>";
    } elseif (is_null($var)) {
        echo "<span class='dd-type'>null</span><span class='dd-null'>null</span>";
    } elseif (is_int($var)) {
        echo "<span class='dd-type'>int</span><span class='dd-int'>$var</span>";
    } elseif (is_float($var)) {
        echo "<span class='dd-type'>float</span><span class='dd-float'>$var</span>";
    } elseif (is_string($var)) {
        echo "<span class='dd-type'>string(" . strlen($var) . ")</span><span class='dd-str'>$var</span>";
    } else {
        var_dump($var);
    }

    echo "</pre>";
    echo "</div>";

    exit;
}

function view(string $route, array $data = []): string
{
    try {
        extract($data);

        $viewPath = str_replace('.', '/', $route);
        $viewFile = "../resources/views/{$viewPath}.php";

        if (!file_exists($viewFile)) {
            header('HTTP/1.1 404 Not Found');
            ErrorHandler::renderError(404, 'Not Found', 'An error occurred while loading the view. Please check if the path is correct.');
        }

        ob_start();
        include $viewFile;
        return ob_get_clean();
    } catch (\Throwable $th) {
        ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
    }
}

function compact_view(string $route, ...$variables): string
{
    try {
        $data = [];
        foreach ($variables as $variable) {
            if (is_string($variable) && isset($$variable)) {
                $data[$variable] = $$variable;
            } elseif (is_array($variable)) {
                $data = array_merge($data, $variable);
            }
        }
        return view($route, $data);
    } catch (\Throwable $th) {
        ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
    }
}

function asset($path = '')
{
    return ($_SERVER['REQUEST_SCHEME'] ?? 'http') . "://" . $_SERVER['HTTP_HOST'] . "/resources/" . ltrim($path, '/');
}

function url($path = '')
{
    $base_url = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . $_SERVER['HTTP_HOST'];
    return $base_url . '/storage/' . $path;
}

function response($body = null, $status = 200, $headers = [])
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

function redirect(string $url, int $status = 302, array $headers = [])
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

function compact(array $array)
{
    return array_filter($array, function ($value) {
        return $value !== null;
    });
}
