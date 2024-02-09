<?php

namespace Lib\Http;

use Lib\Templates\Templates;

/**
 * Class ErrorHandler
 * 
 * this class is used to handle the error response for the application
 */
class ErrorHandler
{
    /**
     * Render an HTML error page.
     *
     * @param int    $errorCode    The HTTP error code.
     * @param string $errorTitle   The title of the error.
     * @param string $errorMessage The error message to display.
     */
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

    /**
     * Render a JSON error response.
     *
     * @param int    $errorCode    The HTTP error code.
     * @param string $errorTitle   The title of the error.
     * @param string $errorMessage The error message to include in the JSON response.
     */
    public static function renderErrorJson($errorCode = 200, $errorTitle = 'Page Not Found', $errorMessage = 'Sorry, we couldn’t find the page you’re looking for.')
    {
        echo response()->json([
            'ERROR_CODE' => $errorCode,
            'ERROR_TITLE' => $errorTitle,
            'ERROR_MESSAGE' => $errorMessage
        ], $errorCode)->send();

        exit;
    }

    /**
     * Render an appropriate error response based on the content type.
     *
     * @param int    $errorCode    The HTTP error code.
     * @param string $errorTitle   The title of the error.
     * @param string $errorMessage The error message to display.
     */
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

        if (strpos($contentType, 'application/json') !== false) {
            self::renderErrorJson($errorCode, $errorTitle, $errorMessage);
        } else {
            self::renderErrorHtml($errorCode, $errorTitle, $errorMessage);
        }
    }
}
