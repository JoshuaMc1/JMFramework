<?php

namespace Lib\Http;

use Lib\Templates\Templates;
use function Lib\Global\{asset, response};

class ErrorHandler
{
    public static function renderError($errorCode = 404, $errorTitle = 'Page Not Found', $errorMessage = 'Sorry, we couldn’t find the page you’re looking for.')
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

    public static function renderErrorJson($errorCode = 404, $errorTitle = 'Page Not Found', $errorMessage = 'Sorry, we couldn’t find the page you’re looking for.')
    {
        return response()->json([
            'ERROR_CODE' => $errorCode,
            'ERROR_TITLE' => $errorTitle,
            'ERROR_MESSAGE' => $errorMessage
        ], $errorCode)->send();
    }
}
