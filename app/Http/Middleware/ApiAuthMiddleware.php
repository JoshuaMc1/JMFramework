<?php

namespace App\Http\Middleware;

use Lib\Exception\ExceptionHandler;
use Lib\Support\{Token, Hash};
use Lib\Http\Middleware\Contracts\MiddlewareInterface;
use Lib\Http\Request;
use Lib\Model\PersonalAccessToken;
use Lib\Router\Exceptions\UnauthorizedAccessException;

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
     * This PHP function handles authentication by checking the validity of a token and throwing an
     * exception if it is invalid.
     * 
     * @param callable next The `` parameter is a callable function that represents the next
     * middleware or handler in the request pipeline. It is responsible for processing the request
     * further down the pipeline.
     * @param Request request The `` parameter is an instance of the `Request` class, which
     * represents an HTTP request made to the server. It contains information about the request, such
     * as the request method, headers, and body.
     * 
     * @return mixed the result of calling the `` callable.
     */
    public function handle(callable $next, Request $request): mixed
    {
        try {
            $token = $request->getToken();

            if ($token === null) {
                throw new UnauthorizedAccessException();
            }

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
}
