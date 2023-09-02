<?php

namespace App\Http\Middleware;

use Lib\Exception\ExceptionHandler;
use Lib\Exception\RouteExceptions\UnauthorizedAccessException;
use Lib\Support\{Token, Hash};
use Lib\Http\Middleware\MiddlewareInterface;
use Lib\Http\Request;
use Lib\Model\PersonalAccessToken;

/**
 * Class ApiAuthMiddleware
 * 
 * @package App\Http\Middleware
 * 
 * this middleware will check if the token is valid, if not it will throw an exception
 */
class ApiAuthMiddleware implements MiddlewareInterface
{
    /**
     * This PHP function handles authentication by checking the token from the request headers,
     * decrypting it, decoding it, and validating it against the stored tokens in the database.
     * 
     * @param callable next The `` parameter is a callable function that represents the next
     * middleware or handler in the request pipeline. It is responsible for processing the request
     * further down the pipeline.
     * @param Request request The `` parameter is an instance of the `Request` class, which
     * represents an HTTP request made to the server. It contains information about the request, such as
     * the request method, headers, query parameters, and request body.
     * 
     * @return mixed the result of the `()` callable.
     */
    public function handle(callable $next, Request $request): mixed
    {
        try {
            $token = $this->getTokenFromHeaders();

            if ($token === null) {
                throw new UnauthorizedAccessException();
            }

            $token = str_replace('Bearer ', '', $token);
            $tokenDecrypt = Hash::decrypt($token);

            $decodedToken = Token::decodeToken($tokenDecrypt);

            if (!$decodedToken['status']) {
                throw new UnauthorizedAccessException($decodedToken['message']);
            }

            $tokenModel = new PersonalAccessToken();
            $foundToken = $tokenModel->where('token', '=', $token)->first();

            if ($foundToken === null) {
                throw new UnauthorizedAccessException('Invalid token');
            }

            return $next();
        } catch (UnauthorizedAccessException $th) {
            ExceptionHandler::handleException($th);
        }
    }

    /**
     * The function retrieves the token from the HTTP headers, or returns null if it is not found.
     * 
     * @return ?string the value of the 'HTTP_AUTHORIZATION' header from the  superglobal
     * array, or null if the header is not set.
     */
    private function getTokenFromHeaders(): ?string
    {
        return $_SERVER['HTTP_AUTHORIZATION'] ?? null;
    }
}
