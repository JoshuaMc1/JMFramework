<?php

use Lib\Http\{ErrorHandler, Response};
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\{CompilerEngine, EngineResolver};
use Illuminate\View\{Factory, FileViewFinder};

function dd($var)
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

function view($view, $data = [])
{
    try {
        $basePath = __DIR__ . '/../..';
        $viewPath = $basePath . '/resources/views/';
        $cachePath = $basePath . '/storage/.cache/views';

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

function asset($path = '')
{
    return ($_SERVER['REQUEST_SCHEME'] ?? 'http') . "://" . $_SERVER['HTTP_HOST'] . "/" . ltrim($path, '/');
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

function isJsonRequest(): bool
{
    $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? '';
    return strpos($acceptHeader, 'application/json') !== false;
}

function now(): string
{
    return date('Y-m-d H:i:s');
}
