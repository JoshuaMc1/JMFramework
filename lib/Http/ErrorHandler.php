<?php

namespace Lib\Http;

use Lib\Templates\Templates;
use Lib\Support\Log;

class ErrorHandler
{
    public static function renderErrorHtml($errorCode = 404, $errorTitle = 'Page Not Found', $errorMessage = 'Sorry, we couldn’t find the page you’re looking for.')
    {
        $template = new Templates();
        $resource = asset('css/app.css');

        $template->render([
            'ERROR_CODE' => $errorCode,
            'ERROR_TITLE' => $errorTitle,
            'ERROR_MESSAGE' => $errorMessage,
            'RESOURCE' => $resource
        ]);
    }

    public static function renderErrorJson($errorCode = 200, $errorTitle = 'Page Not Found', $errorMessage = 'Sorry, we couldn’t find the page you’re looking for.')
    {
        echo response()->json([
            'ERROR_CODE' => $errorCode,
            'ERROR_TITLE' => $errorTitle,
            'ERROR_MESSAGE' => $errorMessage
        ], $errorCode)->send();

        exit;
    }

    public static function renderError($errorCode = 404, $errorTitle = 'Page Not Found', $errorMessage = 'Sorry, we couldn’t find the page you’re looking for.')
    {
        $contentType = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
        $contentType = strtolower($contentType);

        if ($contentType === '*/*') {
            echo response()
                ->text("{$errorCode} {$errorTitle} - {$errorMessage}", 404)
                ->send();
            die();
        }

        $exception = new \ErrorException($errorMessage, $errorCode);

        Log::writeLog('exception error', $errorMessage, [
            'code' => $errorCode,
            'stack_trace' => $exception->getTraceAsString(),
        ]);

        if (strpos($contentType, 'application/json') !== false) {
            self::renderErrorJson($errorCode, $errorTitle, $errorMessage);
        } else {
            self::renderErrorHtml($errorCode, $errorTitle, $errorMessage);
        }
    }
}
