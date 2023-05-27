<?php

namespace App\Middleware;

use Lib\Exception\ExceptionHandler;
use Lib\Exception\RouteExceptions\UnauthorizedAccessException;
use Lib\Support\Token;
use Lib\Http\Middleware\MiddlewareInterface;
use Lib\Http\Request;
use Lib\Model\PersonalAccessToken;
use Lib\Support\Hash;

class ApiAuthMiddleware implements MiddlewareInterface
{
    public function handle(callable $next, Request $request)
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

    private function getTokenFromHeaders()
    {
        return $_SERVER['HTTP_AUTHORIZATION'] ?? null;
    }
}
